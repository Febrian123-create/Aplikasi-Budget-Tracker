<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactiontype')) {
            Schema::create('transactiontype', function (Blueprint $table) {
                $table->id('transactionType_id');
                $table->string('name', 100);
            });

            DB::table('transactiontype')->insert([
                ['transactionType_id' => 1, 'name' => 'Income'],
                ['transactionType_id' => 2, 'name' => 'Expense'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactiontype');
    }
};
