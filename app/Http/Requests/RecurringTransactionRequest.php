<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class RecurringTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description'        => 'required|string|max:255',
            'category_id'        => 'required_if:amount_type,pengeluaran|nullable|exists:category,category_id',
            'amount'             => 'required|numeric|min:1',
            'amount_type'        => 'required|in:pemasukan,pengeluaran',
            'frequency'          => 'required|in:harian,mingguan,bulanan,tahunan',
            'start_date'         => 'required|date',
            'end_date'           => 'nullable|date|after:start_date',
            'reminder_enabled'   => 'nullable|boolean',
            'reminder_days'      => 'nullable|array',
            'reminder_days.*'    => 'integer|in:0,1,3,5,7,10',
            'reminder_channels'  => 'nullable|array',
            'reminder_channels.*'=> 'string|in:email,popup,google_calendar',
            'reminder_message'   => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Deskripsi wajib diisi.',
            'description.max'      => 'Deskripsi maksimal 255 karakter.',
            'category_id.required_if' => 'Kategori wajib dipilih untuk pengeluaran.',
            'category_id.exists'      => 'Kategori tidak ditemukan.',
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
            'end_date.after'            => 'Tanggal berakhir harus setelah tanggal mulai.',
            'reminder_days.*.in'        => 'Hari pengingat tidak valid. Pilih dari: 0, 1, 3, 5, 7, 10.',
            'reminder_channels.*.in'    => 'Channel pengiriman tidak valid.',
            'reminder_message.max'      => 'Pesan reminder maksimal 500 karakter.',
        ];
    }
}
