<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recurring_transaction', function (Blueprint $table) {
            // Status konfirmasi per periode: pending = belum dikonfirmasi, confirmed = sudah bayar, skipped = dilewati
            $table->enum('confirmation_status', ['pending', 'confirmed', 'skipped'])
                  ->default('pending')
                  ->after('status');

            // Kapan user mengkonfirmasi pembayaran
            $table->timestamp('confirmed_at')->nullable()->after('confirmation_status');

            // Simpan tanggal jatuh tempo asli sebelum di-advance (untuk tanggal transaksi saat konfirmasi)
            $table->date('last_due_date')->nullable()->after('confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('recurring_transaction', function (Blueprint $table) {
            $table->dropColumn(['confirmation_status', 'confirmed_at', 'last_due_date']);
        });
    }
};
