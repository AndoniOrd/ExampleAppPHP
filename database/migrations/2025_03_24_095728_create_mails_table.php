<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->json('from');
            $table->json('reply_to')->nullable();
            $table->json('to');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->longText('html')->nullable();
            $table->longText('text')->nullable();
            $table->string('uuid')->unique()->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('mailer');
            $table->string('stream_id')->nullable();
            $table->string('transport')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mails');
    }
};