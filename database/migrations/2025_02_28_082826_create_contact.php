<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ContactStatusEnum;
use App\Enums\SourceEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id'); // INTEGER UNSIGNED primary key
            $table->string('email_address');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('status', ContactStatusEnum::values()); // Use StatusEnum
            $table->enum('source', SourceEnum::values()); // Use SourceEnum
            $table->date('opt_in_date');
            $table->boolean('opt_in_confirmation');
            $table->json('custom_fields');
            $table->date('creation_date');
            $table->date('last_updated_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
