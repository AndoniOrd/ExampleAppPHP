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
        Schema::create('received_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->onDelete('cascade');
            $table->string('message_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_name')->nullable();
            $table->datetime('received_at');
            $table->boolean('is_read')->default(false);
            $table->boolean('has_attachments')->default(false);
            $table->json('headers')->nullable();
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index('message_id');
            $table->index('received_at');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_emails');
    }
};