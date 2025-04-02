<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_planning_id')->nullable()->after('id');
            $table->foreign('campaign_planning_id')
                  ->references('id')
                  ->on('campaign_plannings')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['campaign_planning_id']);
            $table->dropColumn('campaign_planning_id');
        });
    }
};
