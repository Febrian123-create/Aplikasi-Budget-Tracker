<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert category 10 (INCOME) and 11 (WISHLIST) if they don't already exist
        if (Schema::hasTable('category')) {
            $existsIncome = DB::table('category')->where('category_id', 10)->exists();
            if (!$existsIncome) {
                DB::table('category')->insert([
                    'category_id' => 10,
                    'category_name' => 'INCOME',
                    'description' => 'Automatic income category',
                ]);
            }

            $existsWishlist = DB::table('category')->where('category_id', 11)->exists();
            if (!$existsWishlist) {
                DB::table('category')->insert([
                    'category_id' => 11,
                    'category_name' => 'WISHLIST',
                    'description' => 'Automatic wishlist category',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('category')) {
            DB::table('category')->whereIn('category_id', [10, 11])->delete();
        }
    }
};
