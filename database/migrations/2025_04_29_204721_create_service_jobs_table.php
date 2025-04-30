<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->enum('device_type', ['Laptop', 'Desktop', 'Other']);
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('reported_issues');
            $table->text('accessories')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Awaiting Parts', 'Repaired', 'Delivered', 'Canceled'])->default('Pending');
            $table->text('diagnosis')->nullable();
            $table->text('repair_notes')->nullable();
            $table->text('parts_used')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('final_cost', 10, 2)->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_jobs');
    }
};
