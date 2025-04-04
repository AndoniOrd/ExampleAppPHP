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
            $table->id();
            
            // Foreign key to mailing_lists table
            $table->foreignId('list_id')
                ->constrained('mailing_lists')
                ->onDelete('cascade')
                ->comment('Reference to mailing list');
            
            // Foreign key to email_contacts table
            $table->foreignId('contact_id')
                ->constrained('email_contacts')
                ->onDelete('cascade')
                ->comment('Reference to email contact');

            // Subscription details
            $table->dateTime('subscription_date')
                ->default(now())
                ->comment('When the contact was added to the list');
                
            $table->enum('status', ['subscribed', 'unsubscribed', 'pending', 'bounced'])
                ->default('subscribed')
                ->comment('Current subscription status');
                
            $table->dateTime('unsubscribed_at')
                ->nullable()
                ->comment('When the contact unsubscribed');
                
            $table->string('source')
                ->nullable()
                ->comment('How the contact was added to the list');
                
            $table->json('meta')
                ->nullable()
                ->comment('Additional metadata about the relationship');

            // Indexes for better performance
            $table->index(['list_id', 'contact_id']);
            $table->index('status');
            $table->index('unsubscribed_at');

            // Timestamps
            $table->timestamps();
            
            // Composite unique key to prevent duplicates
            $table->unique(['list_id', 'contact_id']);
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