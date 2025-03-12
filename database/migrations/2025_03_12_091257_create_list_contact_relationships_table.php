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
        Schema::create('list_contact_relationships', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign keys
            $table->foreignId('list_id')->constrained('mailing_lists')->onDelete('cascade');
            
            // Changed to reference the camelCase table name
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')
                  ->references('id')
                  ->on('emailContacts')
                  ->onDelete('cascade');

            // Other fields
            $table->date('subscription_date');
            $table->enum('status', ['subscribed', 'unsubscribed', 'pending'])->default('subscribed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_contact_relationships');
    }
};