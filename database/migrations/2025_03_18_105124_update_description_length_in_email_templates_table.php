<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('description', 1000)->change(); // Increase length to 1000 characters
        });
    }
    
    public function down()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('description', 255)->change(); // Revert to original length
        });
    }
};
