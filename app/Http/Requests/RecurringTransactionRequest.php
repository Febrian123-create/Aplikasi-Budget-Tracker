<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * RecurringTransactionRequest — Validasi untuk Fitur 11.
 *
 * Validasi inline agar error ditampilkan segera,
 * bukan setelah submit.
 */
class RecurringTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:category,category_id',
            'amount'      => 'required|numeric|min:1',
            'amount_type' => 'required|in:pemasukan,pengeluaran',
            'frequency'   => 'required|in:harian,mingguan,bulanan,tahunan',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Deskripsi wajib diisi.',
            'description.max'      => 'Deskripsi maksimal 255 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',
            'amount.required'      => 'Nominal wajib diisi.',
            'amount.numeric'       => 'Nominal harus berupa angka.',
            'amount.min'           => 'Nominal harus lebih dari Rp 0.',
            'amount_type.required' => 'Tipe transaksi wajib dipilih.',
            'amount_type.in'       => 'Tipe transaksi tidak valid.',
            'frequency.required'   => 'Frekuensi wajib dipilih.',
            'frequency.in'         => 'Frekuensi tidak valid.',
            'start_date.required'  => 'Tanggal mulai wajib diisi.',
            'start_date.date'      => 'Format tanggal mulai tidak valid.',
            'end_date.date'        => 'Format tanggal berakhir tidak valid.',
            'end_date.after'       => 'Tanggal berakhir harus setelah tanggal mulai.',
        ];
    }
}
