<?php

// File: YYYY_MM_DD_HHMMSS_create_campaign_reports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignReportsTable extends Migration {
    public function up() {
        Schema::create('campaign_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id'); // Match BIGINT UNSIGNED
            $table->integer('total_recipients');
            $table->integer('successful_deliveries');
            $table->integer('hard_bounces')->default(0);
            $table->integer('soft_bounces')->default(0);
            $table->integer('opens_count');
            $table->integer('opens_unique');
            $table->integer('clicks_count');
            $table->integer('clicks_unique');
            $table->float('click_to_open_rate');
            $table->integer('unsubscribes');
            $table->integer('spam_complaints');
            $table->json('device_statistics')->nullable();
            $table->json('geographical_data')->nullable();
            $table->json('time_based_metrics')->nullable();
            $table->float('engagement_score');
            $table->timestamps();

      
 
        });
    }

    public function down() {
        Schema::dropIfExists('campaign_reports');
    }
}