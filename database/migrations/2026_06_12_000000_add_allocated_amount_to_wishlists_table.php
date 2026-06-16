<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            if (!Schema::hasColumn('wishlists', 'allocated_amount')) {
                $table->decimal('allocated_amount', 15, 2)->default(0)->after('target_harga');
            }
        });

        if (Schema::hasColumn('wishlists', 'terkumpul')) {
            DB::table('wishlists')->update(['allocated_amount' => DB::raw('terkumpul')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            if (Schema::hasColumn('wishlists', 'allocated_amount')) {
                $table->dropColumn('allocated_amount');
            }
        });
    }
};
