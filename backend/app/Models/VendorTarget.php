<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VendorTarget extends Model
{
    use HasFactory;

    protected $table = 'vendor_targets';

    protected $fillable = [
        'vendor_id',
        'period_type',
        'target_amount',
        'start_date',
        'end_date',
        'effective_months',
        'notes',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'effective_months' => 'integer',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the monthly equivalent target amount.
     */
    public function getMonthlyEquivalent(): float
    {
        $amount = (float) $this->target_amount;
        switch (strtolower($this->period_type)) {
            case 'quarterly':
                return round($amount / 3, 2);
            case 'half_yearly':
                return round($amount / 6, 2);
            case 'yearly':
                return round($amount / 12, 2);
            case 'custom':
                if ($this->start_date && $this->end_date) {
                    $months = max(1, $this->start_date->diffInMonths($this->end_date) + 1);
                    return round($amount / $months, 2);
                }
                return $amount;
            case 'monthly':
            default:
                return $amount;
        }
    }

    /**
     * Get the target amount in effect for a vendor in a given month.
     */
    public static function getTargetForMonth(int $vendorId, int $year, int $month): float
    {
        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        // 1. Look for a target whose date range encompasses this month
        $target = self::where('vendor_id', $vendorId)
            ->whereDate('start_date', '<=', $monthEnd)
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', $monthStart);
            })
            ->orderBy('start_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($target) {
            return $target->getMonthlyEquivalent();
        }

        // 2. Fallback to vendor's monthly_target in vendors table
        $vendor = Vendor::find($vendorId);
        return $vendor ? (float) ($vendor->monthly_target ?? 0) : 0;
    }

    /**
     * Calculate performance for a specific period (monthly, quarterly, half_yearly, yearly).
     */
    public static function calculatePeriodPerformance(int $vendorId, string $periodType, ?int $year = null, ?int $periodIndex = null): array
    {
        $year = $year ?: (int) now()->year;
        $periodType = strtolower($periodType);

        switch ($periodType) {
            case 'quarterly':
                $quarter = $periodIndex ?: (int) ceil(now()->month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $startMonth + 2;
                $label = "Q{$quarter} {$year}";
                break;

            case 'half_yearly':
                $half = $periodIndex ?: (now()->month <= 6 ? 1 : 2);
                $startMonth = ($half - 1) * 6 + 1;
                $endMonth = $startMonth + 5;
                $label = "H{$half} {$year}";
                break;

            case 'yearly':
                $startMonth = 1;
                $endMonth = 12;
                $label = "Year {$year}";
                break;

            case 'monthly':
            default:
                $startMonth = $periodIndex ?: (int) now()->month;
                $endMonth = $startMonth;
                $monthName = Carbon::createFromDate($year, $startMonth, 1)->format('M');
                $label = "{$monthName} {$year}";
                break;
        }

        $startDate = Carbon::createFromDate($year, $startMonth, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();

        // Calculate total target for this period by summing each month's respective target in effect
        $totalTarget = 0.0;
        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $totalTarget += self::getTargetForMonth($vendorId, $year, $m);
        }

        // Calculate actual sales generated in this period
        $actualSales = (float) VendorCommission::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('transaction_amount');

        // Calculate actual commission earned in this period
        $actualCommission = (float) VendorCommission::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('commission_amount');

        $dealsCount = VendorCommission::where('vendor_id', $vendorId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $achievementPercent = $totalTarget > 0 ? min(500, round(($actualSales / $totalTarget) * 100, 1)) : 0;
        $isAchieved = $totalTarget > 0 && $actualSales >= $totalTarget;
        $shortfall = max(0, $totalTarget - $actualSales);

        return [
            'period_type' => $periodType,
            'label' => $label,
            'year' => $year,
            'start_month' => $startMonth,
            'end_month' => $endMonth,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'target_amount' => round($totalTarget, 2),
            'actual_sales' => round($actualSales, 2),
            'actual_commission' => round($actualCommission, 2),
            'deals_count' => $dealsCount,
            'achievement_percent' => $achievementPercent,
            'is_achieved' => $isAchieved,
            'shortfall' => round($shortfall, 2),
        ];
    }

    /**
     * Get a comprehensive breakdown across all periods for the year.
     */
    public static function getFullYearOverview(int $vendorId, ?int $year = null): array
    {
        $year = $year ?: (int) now()->year;

        // 1. Months Breakdown (1..12)
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $perf = self::calculatePeriodPerformance($vendorId, 'monthly', $year, $m);
            $perf['month_number'] = $m;
            $perf['month_name'] = Carbon::createFromDate($year, $m, 1)->format('M');
            $months[] = $perf;
        }

        // 2. Quarters Breakdown (Q1..Q4)
        $quarters = [];
        for ($q = 1; $q <= 4; $q++) {
            $perf = self::calculatePeriodPerformance($vendorId, 'quarterly', $year, $q);
            $perf['quarter_number'] = $q;
            $quarters[] = $perf;
        }

        // 3. Half-Yearly Breakdown (H1, H2)
        $halfYears = [];
        for ($h = 1; $h <= 2; $h++) {
            $perf = self::calculatePeriodPerformance($vendorId, 'half_yearly', $year, $h);
            $perf['half_number'] = $h;
            $halfYears[] = $perf;
        }

        // 4. Yearly Summary
        $yearly = self::calculatePeriodPerformance($vendorId, 'yearly', $year);

        // 5. Target Schedule History
        $targetsHistory = self::where('vendor_id', $vendorId)
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'period_type' => $t->period_type,
                    'target_amount' => (float) $t->target_amount,
                    'monthly_equivalent' => $t->getMonthlyEquivalent(),
                    'start_date' => $t->start_date ? $t->start_date->format('Y-m-d') : null,
                    'end_date' => $t->end_date ? $t->end_date->format('Y-m-d') : 'Ongoing',
                    'effective_months' => $t->effective_months,
                    'notes' => $t->notes,
                    'created_at' => $t->created_at ? $t->created_at->format('M d, Y') : null,
                ];
            });

        return [
            'year' => $year,
            'current_month' => (int) now()->month,
            'current_quarter' => (int) ceil(now()->month / 3),
            'current_half' => (int) (now()->month <= 6 ? 1 : 2),
            'months' => $months,
            'quarters' => $quarters,
            'half_years' => $halfYears,
            'yearly' => $yearly,
            'targets_history' => $targetsHistory,
        ];
    }
}
