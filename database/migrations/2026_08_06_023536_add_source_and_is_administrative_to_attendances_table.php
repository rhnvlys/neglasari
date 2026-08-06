<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('source')->default('check_in')->after('attendance_status');
            $table->boolean('is_administrative')->default(false)->after('source');
        });

        // Backfill data lama
        DB::table('attendances')->whereNotNull('leave_request_id')->update([
            'source' => 'leave_request',
            'is_administrative' => true,
        ]);
        
        DB::table('attendances')->whereNull('check_in_at')->whereNull('leave_request_id')->whereIn('attendance_status', ['absent'])->update([
            'source' => 'system',
            'is_administrative' => false,
        ]);
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['source', 'is_administrative']);
        });
    }
};
