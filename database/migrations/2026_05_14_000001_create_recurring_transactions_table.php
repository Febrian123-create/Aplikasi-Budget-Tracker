<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('recurring_transaction')) {
            Schema::create('recurring_transaction', function (Blueprint $table) {
                $table->id('recurring_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('category_id');
                $table->decimal('amount', 15, 2);
                $table->enum('frequency', ['harian', 'mingguan', 'bulanan', 'tahunan']);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->date('next_run_date')->index();
                $table->unsignedBigInteger('reminder_id')->nullable();
                $table->string('description', 255);
                $table->enum('amount_type', ['pemasukan', 'pengeluaran']);
                $table->enum('status', ['aktif', 'dijeda', 'selesai'])->default('aktif');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('category_id')->references('category_id')->on('category')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transaction');
    }
};
