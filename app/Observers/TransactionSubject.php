<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Contracts\SubjectInterface;

/**
 * TransactionSubject — Subject dari Observer Pattern.
 * Singleton yang mengelola daftar observer dan mengirim notifikasi
 * ketika ada transaksi yang ditambah, diedit, atau dihapus.
 *
 * Digunakan oleh Fitur 3 (CRUD transaksi) dan Fitur 8 (Chart).
 */
class TransactionSubject implements SubjectInterface
{
    private static ?TransactionSubject $instance = null;

    /** @var ObserverInterface[] */
    private array $observers = [];

    private function __construct()
    {
        // Private constructor untuk Singleton
    }

    /**
     * Mendapatkan instance Singleton dari TransactionSubject.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Subscribe observer ke subject.
     */
    public function subscribe(ObserverInterface $observer): void
    {
        $key = spl_object_id($observer);
        $this->observers[$key] = $observer;
    }

    /**
     * Unsubscribe observer dari subject.
     */
    public function unsubscribe(ObserverInterface $observer): void
    {
        $key = spl_object_id($observer);
        unset($this->observers[$key]);
    }

    /**
     * Beritahu semua observer tentang perubahan transaksi.
     *
     * @param string $event 'created' | 'updated' | 'deleted'
     * @param mixed $data Data transaksi yang berubah
     */
    public function notifyObservers(string $event, mixed $data): void
    {
        foreach ($this->observers as $observer) {
            $observer->onUpdate($event, $data);
        }
    }

    /**
     * Reset instance (untuk testing).
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
