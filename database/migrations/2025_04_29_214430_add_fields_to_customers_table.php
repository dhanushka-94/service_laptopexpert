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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_2')->nullable()->after('phone');
            $table->string('whatsapp_no')->nullable()->after('phone_2');
            
            // Rename existing phone field to phone_1
            $table->renameColumn('phone', 'phone_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Rename phone_1 back to phone
            $table->renameColumn('phone_1', 'phone');
            
            // Drop new columns
            $table->dropColumn(['phone_2', 'whatsapp_no']);
        });
    }
};
