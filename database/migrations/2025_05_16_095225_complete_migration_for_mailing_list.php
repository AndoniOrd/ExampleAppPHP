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
        // First, let's check if the table exists
        if (!Schema::hasTable('mailing_lists')) {
            Schema::create('mailing_lists', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamp('creation_date')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('last_updated_date')->nullable();
                $table->timestamps();
                
                // Add foreign key if User table exists
                if (Schema::hasTable('users')) {
                    $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
                }
            });
        } else {
            // Table exists, so let's add any missing columns
            Schema::table('mailing_lists', function (Blueprint $table) {
                if (!Schema::hasColumn('mailing_lists', 'name')) {
                    $table->string('name');
                }
                
                if (!Schema::hasColumn('mailing_lists', 'creation_date')) {
                    $table->timestamp('creation_date')->nullable();
                }
                
                if (!Schema::hasColumn('mailing_lists', 'description')) {
                    $table->text('description')->nullable();
                }
                
                if (!Schema::hasColumn('mailing_lists', 'owner_id')) {
                    $table->unsignedBigInteger('owner_id')->nullable();
                }
                
                if (!Schema::hasColumn('mailing_lists', 'status')) {
                    $table->string('status')->default('active');
                }
                
                if (!Schema::hasColumn('mailing_lists', 'last_updated_date')) {
                    $table->timestamp('last_updated_date')->nullable();
                }
                
                // Add foreign key if it doesn't exist and User table exists
                if (Schema::hasTable('users') && !Schema::hasColumn('mailing_lists', 'owner_id')) {
                    $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't drop the table here as it might contain important data
        // Instead, just log that this migration would need manual reversal
        \Log::info('Migration to add columns to mailing_lists table would need manual reversal.');
    }
};