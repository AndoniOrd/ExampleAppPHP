<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('email_address')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('status', ['active', 'inactive', 'pending']);
            $table->enum('source', ['web', 'api', 'manual']);
            $table->date('opt_in_date');
            $table->boolean('opt_in_confirmation');
            $table->json('custom_fields')->nullable();
            $table->dateTime('creation_date'); // Changed to dateTime
            $table->dateTime('last_updated_date'); // Changed to dateTime
            $table->timestamps(); // Added timestamps for consistency
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_contacts'); // Fixed to match the table name used in up()
    }
};