<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('day_of_week');
            $table->time('check_in_start');
            $table->time('check_in_time');
            $table->time('check_in_end');
            $table->integer('late_tolerance_minutes');
            $table->time('check_out_start');
            $table->time('check_out_time');
            $table->time('check_out_end');
            $table->boolean('is_workday');
            $table->boolean('is_default');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};