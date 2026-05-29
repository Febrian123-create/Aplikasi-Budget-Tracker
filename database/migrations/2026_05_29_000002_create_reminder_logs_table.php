<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recurring_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->tinyInteger('days_before')->nullable();
            $table->date('scheduled_date');
            $table->string('channel', 30);
            $table->enum('type', ['recurring', 'budget'])->default('recurring');
            $table->timestamp('sent_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('recurring_id')->references('recurring_id')->on('recurring_transaction')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
