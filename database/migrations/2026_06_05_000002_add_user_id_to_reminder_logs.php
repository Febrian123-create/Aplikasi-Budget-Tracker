<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reminder_logs', 'user_id')) {
            Schema::table('reminder_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index('user_id');
            });

            DB::statement('
                UPDATE reminder_logs rl
                JOIN recurring_transaction rt ON rt.recurring_id = rl.recurring_id
                SET rl.user_id = rt.user_id
                WHERE rl.user_id IS NULL AND rl.recurring_id IS NOT NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
