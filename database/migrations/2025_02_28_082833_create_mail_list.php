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
        Schema::create('mailing_lists', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Attributes
            $table->string('name');
            $table->string('description')->nullable();
            $table->date('creation_date');
            $table->date('last_updated_date');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Foreign key to Users table
            $table->enum('status', ['active', 'draft', 'archived']);
            $table->enum('type', ['newsletter', 'promotions', 'updates']); // Adjusted types
            $table->string('tags')->nullable(); // Can store categories/tags as a string or as JSON
            
            // Timestamps for created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailing_lists');
    }
};