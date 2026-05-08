<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fitur 8 — Menambahkan tabel membership dan kolom membership_id di users.
     */
    public function up(): void
    {
        // Buat tabel membership jika belum ada
        if (!Schema::hasTable('membership')) {
            Schema::create('membership', function (Blueprint $table) {
                $table->id('membership_id');
                $table->string('membership_name', 50);
            });

            // Seed data default
            DB::table('membership')->insert([
                ['membership_name' => 'Free'],
                ['membership_name' => 'Premium'],
            ]);
        }

        // Tambah kolom membership_id di users jika belum ada
        if (!Schema::hasColumn('users', 'membership_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('membership_id')->nullable()->default(1)->after('id');
            });

            // Set semua user existing ke Free (membership_id = 1)
            DB::table('users')->whereNull('membership_id')->update(['membership_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
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
