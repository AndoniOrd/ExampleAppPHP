<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('from_name')->nullable()->change();
            $table->string('from_address')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('from_name')->nullable(false)->change();
            $table->string('from_address')->nullable(false)->change();
        });
    }
};
