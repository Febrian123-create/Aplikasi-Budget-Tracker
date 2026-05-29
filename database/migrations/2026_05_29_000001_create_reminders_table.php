<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id('reminder_id');
            $table->unsignedBigInteger('recurring_id');
            $table->unsignedBigInteger('user_id');
            $table->json('reminder_days');
            $table->boolean('reminder_enabled')->default(true);
            $table->json('channels');
            $table->text('custom_message')->nullable();
            $table->timestamps();

            $table->foreign('recurring_id')->references('recurring_id')->on('recurring_transaction')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
