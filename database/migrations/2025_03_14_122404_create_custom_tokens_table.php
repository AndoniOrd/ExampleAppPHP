<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('custom_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // e.g., "api-token"
            $table->string('token', 64);  // Hashed token value
            $table->text('abilities')->nullable(); // JSON abilities (e.g., ["read", "write"])
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_tokens');
    }
};