<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignReportsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('campaign_reports', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_id');
            $table->integer('total_recipients');
            $table->integer('successful_deliveries');
            // Using the already created enum for bounces with values 'hard' and 'soft'
            $table->enum('bounces', ['hard', 'soft']);
            // Separate columns for total and unique counts for opens and clicks
            $table->integer('opens_count');
            $table->integer('opens_unique');
            $table->integer('clicks_count');
            $table->integer('clicks_unique');
            $table->float('click_to_open_rate');
            $table->float('unsubscribes');
            // Renamed column to avoid the slash in the name
            $table->float('complaints_spam_reports');
            $table->text('device_statistics');
            $table->text('geographical_data');
            $table->text('time_based_metrics');
            $table->float('engagement_score');
            $table->timestamps();

            // Foreign key constraint linking campaign_id to the id column on the campaign_plannings table
            $table->foreign('campaign_id')
                  ->references('id')
                  ->on('campaign_plannings')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('campaign_reports');
    }
}
