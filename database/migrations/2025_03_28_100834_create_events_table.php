<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            // Consider using UUID if you need public-facing IDs
            // $table->uuid('uuid')->primary();
            $table->id();
            
            $table->string('name');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            
            // Add nullable fields for better flexibility
            $table->text('description')->nullable();
            $table->string('color')->nullable()->comment('Event color in calendar');
            
            $table->timestamps();
            
            // Add soft deletes if needed
            // $table->softDeletes();
            
            // Indexes for better query performance
            $table->index('starts_at');
            $table->index('ends_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};