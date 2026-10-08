<?php

namespace App\Services;

use App\Models\ProductHsn;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class ProductHsnService
{
    /**
     * Get DataTable response for HSNs
     */
    public function getDatatables($box_id)
    {
        $query = ProductHsn::with('creator', 'updater', 'deleter')->where('box_id', $box_id);
        
        if (request()->has('company_id')) {
            $query->where('company_id', request('company_id'));
        }
        
        $hsns = $query->get();
        return DataTables::of($hsns)
            ->addIndexColumn()
            ->addColumn('checkbox', function($hsn) {
                return '<input type="checkbox" class="hsn-checkbox" value="'.$hsn->id.'">';
            })
            ->addColumn('created_by_name', function($hsn) {
                return $hsn->creator ? $hsn->creator->name : '-';
            })
            ->addColumn('updated_by_name', function($hsn) {
                return $hsn->updater ? $hsn->updater->name : '-';
            })
            ->addColumn('deleted_by_name', function($hsn) {
                return $hsn->deleter ? $hsn->deleter->name : '-';
            })
            ->editColumn('created_at', function($hsn) {
                return $hsn->created_at ? $hsn->created_at->format('d M Y h:i A') : '-';
            })
            ->editColumn('updated_at', function($hsn) {
                return $hsn->updated_at ? $hsn->updated_at->format('d M Y h:i A') : '-';
            })
            ->editColumn('deleted_at', function($hsn) {
                return $hsn->deleted_at ? $hsn->deleted_at->format('d M Y h:i A') : '-';
            })
            ->addColumn('action', function($hsn) {
                return '<a class="btn btn-sm btn-info edit-hsn-btn" href="#" data-bs-toggle="modal" data-bs-target="#editHsnModal" data-id="'.$hsn->id.'" data-code="'.$hsn->code.'" data-short_name="'.$hsn->short_name.'" data-sgst="'.$hsn->sgst.'" data-cgst="'.$hsn->cgst.'" data-igst="'.$hsn->igst.'" data-type="'.$hsn->type.'" data-uqc="'.$hsn->uqc.'" data-cess="'.$hsn->cess.'" data-from_date="'.$hsn->from_date.'" data-to_date="'.$hsn->to_date.'" data-status="'.$hsn->status.'">
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-secondary show-audit-history-btn" href="#" data-url="'.route('accounts.product_hsns.history', ['box_slug' => request()->route('box_slug'), 'id' => $hsn->id]).'">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </a>
                        <a class="btn btn-sm btn-danger delete-hsn-btn" href="#" data-id="'.$hsn->id.'">
                            <i class="fa-regular fa-trash-can"></i>
                        </a>';
            })
            ->editColumn('status', function($hsn) {
                $badgeClass = $hsn->status == 'Yes' ? 'success' : 'danger';
                return '<span class="badge bg-inverse-'.$badgeClass.'">'.$hsn->status.'</span>';
            })
            ->rawColumns(['checkbox', 'action', 'status'])
            ->make(true);
    }

    /**
     * Check for date overlaps and throw ValidationException if found
     */
    public function validateDateOverlap($box_id, $code, $newFrom, $newTo, $ignore_id = null)
    {
        if ($newFrom && $newTo && $newFrom > $newTo) {
            throw ValidationException::withMessages([
                'date_overlap' => ['The from date cannot be after the to date.']
            ]);
        }

        $query = ProductHsn::where('box_id', $box_id)
            ->where('code', $code)
            ->whereNull('deleted_at');
            
        if ($ignore_id) {
            $query->where('id', '!=', $ignore_id);
        }
            
        $query->where(function($q) use ($newFrom, $newTo) {
            if ($newTo) {
                $q->where(function($q2) use ($newTo) {
                    $q2->whereNull('from_date')->orWhere('from_date', '<=', $newTo);
                });
            }
            if ($newFrom) {
                $q->where(function($q2) use ($newFrom) {
                    $q2->whereNull('to_date')->orWhere('to_date', '>=', $newFrom);
                });
            }
        });
        
        if ($query->exists()) {
            throw ValidationException::withMessages([
                'date_overlap' => ['The date range for this HSN/SAC code overlaps with an existing entry.']
            ]);
        }
    }

    /**
     * Store a new Product HSN
     */
    public function store(array $data, $box_id, $user_id)
    {
        $this->validateDateOverlap($box_id, $data['code'], $data['from_date'] ?? null, $data['to_date'] ?? null);

        $data['box_id'] = $box_id;
        $data['created_by'] = $user_id;

        return ProductHsn::create($data);
    }

    /**
     * Update an existing Product HSN
     */
    public function update(array $data, $box_id, $id, $user_id)
    {
        $this->validateDateOverlap($box_id, $data['code'], $data['from_date'] ?? null, $data['to_date'] ?? null, $id);

        $hsn = ProductHsn::where('box_id', $box_id)->findOrFail($id);
        $data['updated_by'] = $user_id;
        $hsn->update($data);

        return $hsn;
    }

    /**
     * Delete a Product HSN
     */
    public function delete($box_id, $id, $user_id)
    {
        $hsn = ProductHsn::where('box_id', $box_id)->findOrFail($id);
        
        if (\App\Models\Product::where('hsn_sac', $hsn->code)->exists()) {
            return [
                'success' => false,
                'message' => "Cannot delete HSN code {$hsn->code} because it is currently used in one or more products."
            ];
        }

        $hsn->update(['deleted_by' => $user_id]);
        $hsn->delete();
        
        return ['success' => true];
    }
    
    /**
     * Bulk delete Product HSNs
     */
    public function bulkDelete($box_id, array $ids, $user_id)
    {
        $hsns = ProductHsn::where('box_id', $box_id)->whereIn('id', $ids)->get();
        $deletedCount = 0;
        $failedCodes = [];
        
        foreach ($hsns as $hsn) {
            if (\App\Models\Product::where('hsn_sac', $hsn->code)->exists()) {
                $failedCodes[] = $hsn->code;
                continue;
            }
            $hsn->update(['deleted_by' => $user_id]);
            $hsn->delete();
            $deletedCount++;
        }
        
        return [
            'success' => $deletedCount > 0 || count($failedCodes) == 0,
            'deleted_count' => $deletedCount,
            'failed_codes' => array_unique($failedCodes)
        ];
    }

    /**
     * Get audit history for an HSN
     */
    public function getHistory($box_id, $id)
    {
        $hsn = ProductHsn::where('box_id', $box_id)->withTrashed()->findOrFail($id);
        
        $auditService = app(\App\Services\AuditHistoryService::class);
        return $auditService->getFormattedHistory($hsn);
    }

    /**
     * Get details of an HSN by code
     */
    public function getDetailsByCode($box_id, $code)
    {
        // Get the currently active one (where status = Yes and not deleted)
        // Since there could be multiple for different date ranges, we take the most relevant or latest.
        return ProductHsn::where('box_id', $box_id)
            ->where('code', $code)
            ->where('status', 'Yes')
            ->orderBy('id', 'desc')
            ->first();
    }
}
