<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * RecurringHelper — Fungsi utilitas terpusat untuk Fitur 11.
 *
 * Berisi logika kalkulasi next_run_date dan pengecekan
 * expired yang dipakai oleh Factory dan Scheduler (DRY).
 */
class RecurringHelper
{
    /**
     * Hitung tanggal eksekusi berikutnya berdasarkan frekuensi.
     *
     * Open/Closed Principle: tambahkan case baru di sini
     * tanpa mengubah logika eksekusi di tempat lain.
     *
     * @param Carbon|string $currentDate Tanggal referensi
     * @param string $frequency 'harian'|'mingguan'|'bulanan'|'tahunan'
     * @return Carbon
     */
    public static function calculateNextRunDate(Carbon|string $currentDate, string $frequency): Carbon
    {
        $date = $currentDate instanceof Carbon ? $currentDate->copy() : Carbon::parse($currentDate);

        return match ($frequency) {
            'harian'   => $date->addDay(),
            'mingguan' => $date->addWeek(),
            'bulanan'  => $date->addMonthNoOverflow(),
            'tahunan'  => $date->addYearNoOverflow(),
            default    => $date->addMonth(), // fallback aman
        };
    }

    /**
     * Cek apakah recurring sudah expired (melewati end_date).
     *
     * @param Carbon|null $nextRunDate Tanggal eksekusi berikutnya
     * @param Carbon|null $endDate Tanggal berakhir (null = tanpa batas)
     * @return bool
     */
    public static function isExpired(?Carbon $nextRunDate, ?Carbon $endDate): bool
    {
        if ($endDate === null) {
            return false; // Tidak ada batas waktu
        }

        if ($nextRunDate === null) {
            return true;
        }

        return $nextRunDate->greaterThan($endDate);
    }

    /**
     * Mendapatkan label frekuensi dalam Bahasa Indonesia.
     */
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

    /**
     * Daftar frekuensi yang tersedia berdasarkan membership.
     *
     * Free: hanya bulanan & tahunan
     * Premium: semua frekuensi
     */
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

        // Free user: hanya bulanan & tahunan
        return [
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
        ];
    }
}
