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
            $table->string('description')->nullable();
            $table->string('subject_line');
            $table->text('html_content');
            $table->text('plain_text_version');
            $table->foreignId('creator'); // Foreign key to users.id
            $table->date('creation_date');
            $table->date('last_updated_date');
            $table->string('category');
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->string('preview_image_url')->nullable();
            $table->timestamps(); // Adds created_at and updated_at

            // Indexes
            $table->index('status');
            $table->index('category');
            $table->index('creation_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
};