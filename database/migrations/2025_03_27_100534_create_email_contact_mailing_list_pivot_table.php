<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_contact_mailing_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_contact_id')->constrained('email_contacts')->onDelete('cascade');
            $table->foreignId('mailing_list_id')->constrained('mailing_lists')->onDelete('cascade');
            $table->string('status')->default('subscribed');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->unique(['email_contact_id', 'mailing_list_id'], 'contact_list_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_contact_mailing_list');
    }
};