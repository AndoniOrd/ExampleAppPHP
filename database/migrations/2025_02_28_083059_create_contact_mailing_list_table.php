<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_mailing_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('mailing_list_id');
            $table->enum('status', ['subscribed', 'unsubscribed', 'pending'])->default('unsubscribed');
            $table->timestamp('subscription_date')->nullable();
            
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('mailing_list_id')->references('id')->on('mailing_lists')->onDelete('cascade');
            
            $table->unique(['contact_id', 'mailing_list_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_mailing_list');
    }
};