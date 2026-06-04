<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'google_calendar_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('google_calendar_token')->nullable()->after('membership_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_calendar_token');
        });
    }
};
