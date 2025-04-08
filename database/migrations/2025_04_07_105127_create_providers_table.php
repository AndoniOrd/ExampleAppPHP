<?php

// database/migrations/[timestamp]_create_providers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            
            // SMTP credentials
            $table->string('smtp_host');
            $table->integer('smtp_port');
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_username');
            $table->string('smtp_password');
            
            // IMAP credentials
            $table->string('imap_host');
            $table->integer('imap_port');
            $table->string('imap_encryption')->nullable();
            $table->string('imap_username');
            $table->string('imap_password');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('providers');
    }
};