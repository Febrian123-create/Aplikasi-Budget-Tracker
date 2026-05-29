<?php

namespace App\Helpers;

use Carbon\Carbon;


class RecurringHelper
{
    
    public static function calculateNextRunDate(Carbon|string $currentDate, string $frequency): Carbon
    {
        $date = $currentDate instanceof Carbon ? $currentDate->copy() : Carbon::parse($currentDate);

        return match ($frequency) {
            'harian'   => $date->addDay(),
            'mingguan' => $date->addWeek(),
            'bulanan'  => $date->addMonthNoOverflow(),
            'tahunan'  => $date->addYearNoOverflow(),
            default    => $date->addMonth(), 
        };
    }

    
    public static function isExpired(?Carbon $nextRunDate, ?Carbon $endDate): bool
    {
        if ($endDate === null) {
            return false; 
        }

        if ($nextRunDate === null) {
            return true;
        }

        return $nextRunDate->greaterThan($endDate);
    }

    
    public static function getFrequencyLabel(string $frequency): string
    {
        return match ($frequency) {
            'harian'   => 'Setiap Hari',
            'mingguan' => 'Setiap Minggu',
            'bulanan'  => 'Setiap Bulan',
            'tahunan'  => 'Setiap Tahun',
            default    => ucfirst($frequency),
        };
    }

    
    public static function getAvailableFrequencies(string $membershipName): array
    {
        $all = [
            'harian'   => 'Harian',
            'mingguan' => 'Mingguan',
            'bulanan'  => 'Bulanan',
            'tahunan'  => 'Tahunan',
        ];

        if (strtolower($membershipName) === 'premium') {
            return $all;
        }

        
        return [
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
        ];
    }
}
