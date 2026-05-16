<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('membership', 'price')) {
            Schema::table('membership', function (Blueprint $table) {
                $table->decimal('price', 15, 2)->default(0)->after('membership_name');
            });

            // Update prices
            DB::table('membership')->where('membership_id', 1)->update(['price' => 0]);
            DB::table('membership')->where('membership_id', 2)->update(['price' => 99000]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('membership', 'price')) {
            Schema::table('membership', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }
};
