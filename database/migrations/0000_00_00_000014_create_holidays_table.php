<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type');
            $table->text('description')->nullable();
            $table->boolean('applies_to_all')->default(true);
            $table->boolean('is_active');
            $table->timestamps();
        });
        
        Schema::create('holiday_employee', function (Blueprint $table) {
            $table->foreignId('holiday_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unique(['holiday_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_employee');
        Schema::dropIfExists('holidays');
    }
};