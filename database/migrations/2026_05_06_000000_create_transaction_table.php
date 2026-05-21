<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transaction')) {
            Schema::create('transaction', function (Blueprint $table) {
                $table->id('transaction_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('transactionType_id')->nullable();
                $table->double('total_amount')->nullable();
                $table->dateTime('transaction_date')->nullable();
                $table->string('description', 300)->nullable();

                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('category_id')->references('category_id')->on('category')->onDelete('cascade');
                $table->foreign('transactionType_id')->references('transactionType_id')->on('transactiontype')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
