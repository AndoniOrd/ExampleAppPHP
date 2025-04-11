<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('email_contacts', function (Blueprint $table) {
            $table->foreignId('mailing_list_id')
                  ->nullable()
                  ->constrained('mailing_lists')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('email_contacts', function (Blueprint $table) {
            $table->dropForeign(['mailing_list_id']);
            $table->dropColumn('mailing_list_id');
        });
    }
};