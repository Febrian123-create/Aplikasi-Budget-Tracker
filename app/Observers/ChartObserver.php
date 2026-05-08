<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Services\ChartService;

/**
 * ChartObserver — Observer untuk Fitur 8.
 * Menerima notifikasi dari TransactionSubject dan
 * menandai bahwa data chart perlu di-refresh.
 *
 * Pada server-rendered app (Laravel Blade), observer ini
 * memastikan data chart selalu fresh saat halaman dibuka,
 * dan bisa digunakan untuk future real-time updates via WebSocket.
 */
class ChartObserver implements ObserverInterface
{
    private bool $needsRefresh = false;

    public function __construct(
        private ChartService $chartService
    ) {}

    /**
     * Dipanggil ketika transaksi berubah (created/updated/deleted).
     * Menandai chart perlu di-refresh.
     */
    public function onUpdate(string $event, mixed $data): void
    {
        $this->needsRefresh = true;

        // Untuk future real-time: broadcast event ke frontend
        // event(new ChartDataUpdated($data));
    }

    /**
     * Cek apakah chart perlu di-refresh.
     */
    public function needsRefresh(): bool
    {
        return $this->needsRefresh;
    }

    /**
     * Reset flag refresh setelah data diambil.
     */
    public function resetRefresh(): void
    {
        $this->needsRefresh = false;
    }

    /**
     * Subscribe observer ke TransactionSubject.
     */
    public function subscribe(): void
    {
        TransactionSubject::getInstance()->subscribe($this);
    }

    /**
     * Unsubscribe observer dari TransactionSubject.
     */
    public function unsubscribe(): void
    {
        TransactionSubject::getInstance()->unsubscribe($this);
    }
}
