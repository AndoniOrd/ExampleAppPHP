<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up()
    {
        Schema::create('campaign_plannings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('email_template_id');
            $table->string('mailing_list_id');
            $table->timestamp('scheduled_time');
            $table->string('time_zone');
            $table->string('status_type');
            $table->date('creation_date');
            $table->string('scheduled_by');
            $table->string('send_from_email');
            $table->string('send_from_name');
            $table->string('reply_to_email');
            $table->string('tracking_options');
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down()
    {
        Schema::dropIfExists('campaign_plannings');
    }
};
