<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id('budget_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->decimal('amount', 15, 2);
            $table->enum('period', ['bulanan', 'mingguan', 'tahunan'])->default('bulanan');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('category_id')->on('category')->onDelete('cascade');
            $table->unique(['user_id', 'category_id', 'period', 'start_date'], 'unique_user_category_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
