<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('email_contacts', function (Blueprint $table) {
            $table->boolean('has_crm')->default(false)->after('source');
        });
    }
    
    public function down()
    {
        Schema::table('email_contacts', function (Blueprint $table) {
            $table->dropColumn('has_crm');
        });
    }
};
