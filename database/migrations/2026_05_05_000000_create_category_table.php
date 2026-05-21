<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('category')) {
            Schema::create('category', function (Blueprint $table) {
                $table->id('category_id');
                $table->string('category_name', 100);
                $table->string('description', 300)->nullable();
            });

            DB::table('category')->insert([
                ['category_id' => 1, 'category_name' => 'FOOD', 'description' => 'Food related expenses'],
                ['category_id' => 2, 'category_name' => 'TRANSPORT', 'description' => 'Transportation costs'],
                ['category_id' => 3, 'category_name' => 'BILLS', 'description' => 'Utility bills'],
                ['category_id' => 4, 'category_name' => 'CLOTHES', 'description' => 'Clothing purchases'],
                ['category_id' => 5, 'category_name' => 'HEALTH', 'description' => 'Healthcare expenses'],
                ['category_id' => 6, 'category_name' => 'GROCERY', 'description' => 'Daily groceries'],
                ['category_id' => 7, 'category_name' => 'HOUSE', 'description' => 'Household expenses'],
                ['category_id' => 8, 'category_name' => 'COMMUNICATIONS', 'description' => 'Phone/Internet costs'],
                ['category_id' => 9, 'category_name' => 'OTHER', 'description' => 'Miscellaneous'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category');
    }
};
