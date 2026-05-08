<?php

namespace App\Helpers;

/**
 * ChartHelper — Fungsi utilitas untuk Fitur 8.
 * Semua fungsi format ada di sini untuk menghindari duplikasi.
 */
class ChartHelper
{
    /**
     * Format angka ke format Rupiah Indonesia.
     * Contoh: 2500000 → "Rp 2.500.000"
     */
    public static function formatRupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format angka ke format ringkas.
     * Contoh: 500000 → "500rb", 1500000 → "1,5jt", 1000000000 → "1M"
     */
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

    /**
     * Format bulan ke singkatan Bahasa Indonesia.
     * Contoh: 1 → "Jan", 2 → "Feb", ..., 12 → "Des"
     */
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

    /**
     * Format bulan ke nama lengkap Bahasa Indonesia.
     */
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

    /**
     * Hitung persentase dan format ke string.
     * Contoh: hitungPersentase(345, 1000) → "34,5%"
     */
    public static function hitungPersentase(float|int $nilai, float|int $total): string
    {
        if ($total == 0) {
            return '0%';
        }

        $persen = ($nilai / $total) * 100;

        return str_replace('.', ',', number_format($persen, 1)) . '%';
    }

    /**
     * Palet warna untuk chart — warna-warna yang harmonis.
     * Segmen pertama (terbesar) mendapat warna paling tegas.
     */
    public static function getChartColors(): array
    {
        return [
            '#6366F1', // Indigo — warna tegas untuk segmen terbesar
            '#22C55E', // Green
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#06B6D4', // Cyan
            '#EC4899', // Pink
            '#8B5CF6', // Violet
            '#14B8A6', // Teal
        ];
    }
}
