<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    

    public function up(): void
    {
        
        if (!Schema::hasTable('membership')) {
            Schema::create('membership', function (Blueprint $table) {
                $table->id('membership_id');
                $table->string('membership_name', 50);
            });

            
            DB::table('membership')->insert([
                ['membership_name' => 'Free'],
                ['membership_name' => 'Premium'],
            ]);
        }

        
        if (!Schema::hasColumn('users', 'membership_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('membership_id')->nullable()->default(1)->after('id');
            });

            
            DB::table('users')->whereNull('membership_id')->update(['membership_id' => 1]);
        }
    }

    

    public function down(): void
    {
        if (Schema::hasColumn('users', 'membership_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('membership_id');
            });
        }

        Schema::dropIfExists('membership');
    }
};
