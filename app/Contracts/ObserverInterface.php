<?php

namespace App\Contracts;

/**
 * Observer Interface — bagian dari Observer Pattern.
 * Setiap observer yang ingin menerima notifikasi dari Subject
 * harus mengimplementasikan interface ini.
 */
interface ObserverInterface
{
    /**
     * Dipanggil oleh Subject ketika ada perubahan data.
     *
     * @param string $event Nama event (created, updated, deleted)
     * @param mixed $data Data yang berubah
     * @return void
     */
    public function onUpdate(string $event, mixed $data): void;
}
