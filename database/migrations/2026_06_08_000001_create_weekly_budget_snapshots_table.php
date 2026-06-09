<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_budget_snapshots', function (Blueprint $table) {
            $table->id('snapshot_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('category_id');
            $table->unsignedBigInteger('budget_id');
            $table->date('week_start');
            $table->date('week_end');
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2);
            $table->enum('status', ['underbudget', 'overbudget'])->default('underbudget');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('category_id')->on('category')->onDelete('cascade');
            $table->foreign('budget_id')->references('budget_id')->on('budgets')->onDelete('cascade');
            $table->unique(['user_id', 'category_id', 'week_start'], 'unique_user_category_week');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_budget_snapshots');
    }
};
