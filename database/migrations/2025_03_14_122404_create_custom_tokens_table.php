<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomTokensTable extends Migration
{
    public function up()
    {
        Schema::create('custom_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable'); // Creates `tokenable_id` and `tokenable_type` columns
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_tokens');
    }
}