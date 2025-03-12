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

            // Foreign keys with bigInteger type to match the mailing_lists and email_contacts primary keys
            $table->foreignId('list_id')->constrained('mailing_lists')->onDelete('cascade');
            $table->foreignId('contact_id')->constrained('email_contacts')->onDelete('cascade');

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
