<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject_line')->nullable();
            $table->string('subject')->nullable(); // Adding this as alternate naming convention
            $table->text('description')->nullable(); // Make description nullable
            $table->longText('html_content')->nullable();
            $table->longText('content')->nullable(); // Adding this as alternate naming convention
            $table->longText('plain_text_version')->nullable();
            $table->unsignedBigInteger('creator')->nullable();
            $table->date('creation_date')->nullable();
            $table->date('last_updated_date')->nullable();
            $table->string('category')->nullable();
            $table->string('status')->nullable();
            $table->string('preview_image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
};