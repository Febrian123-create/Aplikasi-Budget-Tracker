<?php

namespace App\Helpers;


class ChartHelper
{
    
    public static function formatRupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    
    public static function formatRupiahRingkas(float|int $amount): string
    {
        if ($amount >= 1_000_000_000) {
            $value = $amount / 1_000_000_000;
            return rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',') . 'M';
        }

        if ($amount >= 1_000_000) {
            $value = $amount / 1_000_000;
            return rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',') . 'jt';
        }

        if ($amount >= 1_000) {
            $value = $amount / 1_000;
            return rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',') . 'rb';
        }

        return (string) $amount;
    }

    
    public static function formatBulan(int $bulan): string
    {
        $namaBulan = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'Mei',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Agu',
            9  => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        return $namaBulan[$bulan] ?? '';
    }

    
    public static function formatBulanLengkap(int $bulan): string
    {
        $namaBulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $namaBulan[$bulan] ?? '';
    }

    
    public static function hitungPersentase(float|int $nilai, float|int $total): string
    {
        if ($total == 0) {
            return '0%';
        }

        $persen = ($nilai / $total) * 100;

        return str_replace('.', ',', number_format($persen, 1)) . '%';
    }

    
    public static function getChartColors(): array
    {
        return [
            '#6366F1', 
            '#22C55E', 
            '#F59E0B', 
            '#EF4444', 
            '#06B6D4', 
            '#EC4899', 
            '#8B5CF6', 
            '#14B8A6', 
        ];
    }
}
