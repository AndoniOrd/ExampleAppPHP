<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mailing_lists', function (Blueprint $table) {
            // Change date columns to datetime to store time information
            $table->datetime('creation_date')->change();
            $table->datetime('last_updated_date')->change();
            
            // Add created_by column if it doesn't exist
            if (!Schema::hasColumn('mailing_lists', 'created_by')) {
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->after('owner_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mailing_lists', function (Blueprint $table) {
            // Revert back to date columns
            $table->date('creation_date')->change();
            $table->date('last_updated_date')->change();
            
            // Drop created_by column if it was added
            if (Schema::hasColumn('mailing_lists', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};