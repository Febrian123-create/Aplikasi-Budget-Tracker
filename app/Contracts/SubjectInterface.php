<?php

namespace App\Contracts;

/**
 * Subject Interface — bagian dari Observer Pattern.
 * Subject mengelola daftar observer dan memberitahu mereka
 * saat terjadi perubahan data.
 */
interface SubjectInterface
{
    /**
     * Tambahkan observer ke daftar subscriber.
     */
    public function subscribe(ObserverInterface $observer): void;

    /**
     * Hapus observer dari daftar subscriber.
     */
    public function unsubscribe(ObserverInterface $observer): void;

    /**
     * Beritahu semua observer tentang perubahan data.
     *
     * @param string $event Nama event (created, updated, deleted)
     * @param mixed $data Data yang berubah
     */
    public function notifyObservers(string $event, mixed $data): void;
}
