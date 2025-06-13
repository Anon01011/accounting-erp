<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\AssetTransaction;

trait AssetCalculations
{
    public function getCurrentBookValue()
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail) {
            return 0;
        }

        $purchasePrice = $assetDetail->purchase_price;
        $accumulatedDepreciation = $this->getAccumulatedDepreciation();

        return max(0, $purchasePrice - $accumulatedDepreciation);
    }

    public function getAccumulatedDepreciation()
    {
        return $this->assetTransactions()
            ->where('type', AssetTransaction::TYPE_DEPRECIATION)
            ->sum('amount');
    }

    public function getNextDepreciationDate()
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail) {
            return Carbon::now();
        }

        $lastDepreciation = $this->assetTransactions()
            ->where('type', AssetTransaction::TYPE_DEPRECIATION)
            ->latest('date')
            ->first();

        if (!$lastDepreciation) {
            return $assetDetail->purchase_date;
        }

        $nextDate = Carbon::parse($lastDepreciation->date)->addYear();
        
        if ($this->getCurrentBookValue() <= 0) {
            return Carbon::now();
        }

        return $nextDate;
    }

    public function calculateDepreciationAmount($date = null)
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail) {
            return 0;
        }

        $method = $assetDetail->depreciation_method;
        $rate = $assetDetail->depreciation_rate;
        $life = $assetDetail->useful_life;
        $cost = $assetDetail->purchase_price;
        $currentValue = $this->getCurrentBookValue();
        $date = $date ? Carbon::parse($date) : Carbon::now();

        if ($currentValue <= 0) {
            return 0;
        }

        switch ($method) {
            case 'straight_line':
                return $this->calculateStraightLineDepreciation($cost, $life, $date);
            case 'declining_balance':
                return $this->calculateDecliningBalanceDepreciation($currentValue, $rate, $date);
            case 'sum_of_years':
                return $this->calculateSumOfYearsDepreciation($cost, $life, $date);
            case 'double_declining':
                return $this->calculateDoubleDecliningDepreciation($cost, $life, $currentValue, $date);
            case 'units_of_production':
                return $this->calculateUnitsOfProductionDepreciation($cost, $life, $date);
            default:
                return 0;
        }
    }

    private function calculateStraightLineDepreciation($cost, $life, $date)
    {
        if ($life <= 0) {
            return 0;
        }

        $annualDepreciation = $cost / $life;
        $daysInYear = $date->isLeapYear() ? 366 : 365;
        $daysInFiscalYear = $this->getDaysInFiscalYear($date);
        
        return ($annualDepreciation / $daysInYear) * $daysInFiscalYear;
    }

    private function calculateDecliningBalanceDepreciation($currentValue, $rate, $date)
    {
        if ($rate <= 0) {
            return 0;
        }

        $annualDepreciation = $currentValue * ($rate / 100);
        $daysInYear = $date->isLeapYear() ? 366 : 365;
        $daysInFiscalYear = $this->getDaysInFiscalYear($date);
        
        return ($annualDepreciation / $daysInYear) * $daysInFiscalYear;
    }

    private function calculateSumOfYearsDepreciation($cost, $life, $date)
    {
        if ($life <= 0) {
            return 0;
        }

        $remainingLife = $life - $this->getAge();
        if ($remainingLife <= 0) {
            return 0;
        }

        $sum = ($life * ($life + 1)) / 2;
        $annualDepreciation = ($cost * $remainingLife) / $sum;
        $daysInYear = $date->isLeapYear() ? 366 : 365;
        $daysInFiscalYear = $this->getDaysInFiscalYear($date);
        
        return ($annualDepreciation / $daysInYear) * $daysInFiscalYear;
    }

    private function calculateDoubleDecliningDepreciation($cost, $life, $currentValue, $date)
    {
        if ($life <= 0) {
            return 0;
        }

        $rate = (2 / $life) * 100;
        return $this->calculateDecliningBalanceDepreciation($currentValue, $rate, $date);
    }

    private function calculateUnitsOfProductionDepreciation($cost, $life, $date)
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail || !$assetDetail->total_units || $assetDetail->total_units <= 0) {
            return 0;
        }

        $depreciationPerUnit = $cost / $assetDetail->total_units;
        $unitsThisYear = $this->getUnitsThisYear($date);
        
        return $depreciationPerUnit * $unitsThisYear;
    }

    private function getDaysInFiscalYear($date)
    {
        $fiscalYearStart = config('accounting.fiscal_year_start', '01-01');
        $fiscalYearEnd = config('accounting.fiscal_year_end', '12-31');
        
        $startDate = Carbon::createFromFormat('m-d', $fiscalYearStart)->year($date->year);
        $endDate = Carbon::createFromFormat('m-d', $fiscalYearEnd)->year($date->year);
        
        if ($date->lt($startDate)) {
            $startDate->subYear();
        }
        if ($date->gt($endDate)) {
            $endDate->addYear();
        }
        
        return $date->diffInDays($startDate) + 1;
    }

    private function getUnitsThisYear($date)
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail) {
            return 0;
        }

        $startOfYear = $date->copy()->startOfYear();
        $endOfYear = $date->copy()->endOfYear();
        
        return $this->assetTransactions()
            ->where('type', 'production')
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->sum('units');
    }

    public function getAge()
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail || !$assetDetail->purchase_date) {
            return 0;
        }

        return $assetDetail->purchase_date->diffInYears(Carbon::now());
    }

    public function getRemainingLife()
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail) {
            return 0;
        }

        return max(0, $assetDetail->useful_life - $this->getAge());
    }

    public function getDepreciationPercentage()
    {
        $assetDetail = $this->assetDetails->first();
        if (!$assetDetail || $assetDetail->purchase_price <= 0) {
            return 0;
        }

        $age = $this->getAge();
        $life = $assetDetail->useful_life;

        if ($life <= 0) {
            return 0;
        }

        return min(100, ($age / $life) * 100);
    }

    public function isFullyDepreciated()
    {
        return $this->getCurrentBookValue() <= 0;
    }

    public function getMaintenanceCost()
    {
        return $this->maintenanceRecords()
            ->where('status', true)
            ->sum('cost');
    }

    public function getTotalCost()
    {
        return $this->getCurrentBookValue() + $this->getMaintenanceCost();
    }
} 