<?php

namespace App\Services\DataTables;

use App\Models\BoxCustomField;
use Illuminate\Contracts\Database\Query\Builder as QueryContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\EloquentDataTable;

class FastLeadsDataTable extends EloquentDataTable
{
    protected int $boxId;

    public function __construct(EloquentBuilder $builder, int $boxId)
    {
        parent::__construct($builder);
        $this->boxId = $boxId;
    }

    /**
     * Override results() to intercept the paginated slice of leads
     * and eager-load all per-row related data in a small set of bulk queries.
     *
     * @return Collection
     */
    public function results(): Collection
    {
        if (!isset($this->results)) {
            $this->results = parent::results();
            $this->eagerLoadBatchData($this->results);
        }

        return $this->results;
    }

    /**
     * Override filteredCount to avoid running an identical duplicate COUNT(*) query
     * when no global or column search filters were applied.
     *
     * @return int
     */
    public function filteredCount(): int
    {
        if (isset($this->filteredRecords)) {
            return $this->filteredRecords;
        }

        $searchValue = $this->request->searchKeyword();
        $hasColumnSearch = false;
        $columns = (array) $this->request->columns();
        foreach ($columns as $col) {
            if (!empty($col['search']['value'])) {
                $hasColumnSearch = true;
                break;
            }
        }

        if (empty($searchValue) && !$hasColumnSearch) {
            return $this->filteredRecords = $this->totalCount();
        }

        return $this->filteredRecords = $this->count();
    }

    /**
     * Prepare optimized count query by removing unnecessary LEFT JOINs on primary keys
     * that do not alter the row count.
     */
    public function prepareCountQuery(): QueryContract
    {
        $builder = parent::prepareCountQuery();

        $query = $builder instanceof EloquentBuilder ? $builder->getQuery() : $builder;
        if (!empty($query->joins)) {
            $query->joins = array_values(array_filter($query->joins, function ($join) {
                return $join->table !== 'activity_types';
            }));
        }

        return $builder;
    }

    /**
     * Batch pre-fetch receipts, division counts, last activities, and custom fields
     * for the exact subset of leads on the current page.
     *
     * @param Collection $leads
     * @return void
     */
    protected function eagerLoadBatchData(Collection $leads): void
    {
        if ($leads->isEmpty()) {
            return;
        }

        $leadIds = $leads->pluck('id')->filter()->unique()->values()->all();
        if (empty($leadIds)) {
            return;
        }

        // 1. Batch fetch receipts (ordered by created_at desc) to resolve:
        //    - last_receipt_amount (latest receipt amount)
        //    - last_receipt_date (latest receipt date)
        //    - receipts_sum_amount (sum of all receipts)
        $receiptsByLead = \App\Models\Accounts\Receipt::whereIn('lead_id', $leadIds)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get(['lead_id', 'amount', 'receipt_date', 'created_at'])
            ->groupBy('lead_id');

        // 2. Batch fetch division areas counts
        $divisionCounts = \App\Models\DivisionArea::whereIn('lead_id', $leadIds)
            ->whereNull('deleted_at')
            ->select('lead_id', DB::raw('count(*) as count'))
            ->groupBy('lead_id')
            ->pluck('count', 'lead_id');

        // 3. Batch fetch latest past activity date
        $now = \Carbon\Carbon::now();
        $lastActivities = \App\Models\Activity::whereIn('activity_id', $leadIds)
            ->where('activity_date', '<', $now)
            ->whereNull('deleted_at')
            ->orderBy('activity_date', 'desc')
            ->get(['activity_id', 'activity_date'])
            ->groupBy('activity_id')
            ->map(fn($acts) => $acts->first()->activity_date);

        // 4. Batch fetch custom fields table data (data_{boxId})
        $customFields = BoxCustomField::where('box_id', $this->boxId)
            ->where('object', 'leads')
            ->where('status', 'Enabled')
            ->get();

        $tableName = 'data_' . $this->boxId;
        $customDataMap = collect();

        if (Schema::hasTable($tableName) && $customFields->isNotEmpty()) {
            $cols = array_merge(['lead_id'], $customFields->pluck('col_name')->toArray());
            $customDataMap = DB::table($tableName)
                ->whereIn('lead_id', $leadIds)
                ->get($cols)
                ->keyBy('lead_id');
        }

        // Attach preloaded data to model attributes for O(1) in-memory resolution
        foreach ($leads as $lead) {
            $leadReceipts = $receiptsByLead->get($lead->id, collect());
            $latestReceipt = $leadReceipts->first();

            $lead->preloaded_last_receipt_amount = $latestReceipt ? (string)$latestReceipt->amount : null;
            $lead->preloaded_last_receipt_date = $latestReceipt && $latestReceipt->receipt_date
                ? \Carbon\Carbon::parse($latestReceipt->receipt_date)->format('Y-m-d H:i:s')
                : null;

            $sumAmount = $leadReceipts->sum('amount');
            $lead->preloaded_receipts_sum_amount = $sumAmount > 0 ? (string)$sumAmount : null;

            $lead->preloaded_division_areas_count = $divisionCounts->get($lead->id, 0);

            $lastActDate = $lastActivities->get($lead->id);
            $lead->preloaded_last_activity_date = $lastActDate
                ? \Carbon\Carbon::parse($lastActDate)->format('Y-m-d H:i:s')
                : null;

            // Custom table data
            if ($customFields->isEmpty()) {
                $lead->preloaded_custom_data = (object) [];
            } else {
                $firstField = $customFields->first();
                $testKey = 'custom_' . $firstField->col_name;

                if (array_key_exists($testKey, $lead->getAttributes())) {
                    $cData = [];
                    foreach ($customFields as $field) {
                        $key = 'custom_' . $field->col_name;
                        $cData[$field->col_name] = $lead->$key ?? null;
                    }
                    $lead->preloaded_custom_data = (object) $cData;
                } else {
                    $customRow = $customDataMap->get($lead->id);
                    if ($customRow) {
                        $cData = (array) $customRow;
                        unset($cData['lead_id']);
                        $lead->preloaded_custom_data = (object) $cData;
                    } else {
                        $lead->preloaded_custom_data = (object) [];
                    }
                }
            }
        }
    }
}
