<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    

    public function up(): void
    {
        if (!Schema::hasTable('role')) {
            Schema::create('role', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->primary();
                $table->string('role_name', 50);
            });

            
            DB::table('role')->insert([
                ['role_id' => 1, 'role_name' => 'admin'],
                ['role_id' => 2, 'role_name' => 'user'],
            ]);
        }
    }

    

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
