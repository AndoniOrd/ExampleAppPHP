<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('email_contacts', function (Blueprint $table) {
            $table->string('tracking_token')->unique()->nullable();
            $table->string('delivery_status')->default('pending');
            $table->timestamp('delivered_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('email_contacts', function (Blueprint $table) {
            $table->dropColumn('tracking_token');
            $table->dropColumn('delivery_status');
            $table->dropColumn('delivered_at');
        });
    }
};
