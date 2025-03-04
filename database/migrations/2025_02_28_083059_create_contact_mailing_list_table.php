<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contact_mailing_list')) {
            Schema::create('contact_mailing_list', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contact_id');
                $table->unsignedBigInteger('mailing_list_id');
                $table->timestamps();

                $table->foreign('contact_id')
                      ->references('id')
                      ->on('contacts')
                      ->onDelete('cascade');

                $table->foreign('mailing_list_id')
                      ->references('id')
                      ->on('mailing_lists')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_mailing_list');
    }
};