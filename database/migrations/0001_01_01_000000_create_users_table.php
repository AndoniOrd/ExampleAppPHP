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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email_address')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('phone_number')->nullable();
        $table->enum('role', ['admin', 'editor', 'viewer'])->default('viewer');
        $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active');
        $table->dateTime('creation_date')->useCurrent();
        $table->dateTime('last_login')->nullable();
        $table->string('company_name')->nullable();
        $table->string('company_address')->nullable();
        $table->string('vat_tax_id')->nullable();
        $table->string('industry')->nullable();
        $table->integer('company_size')->nullable();
        $table->string('website')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'first_name',
            'last_name',
            'email_address',
            'phone_number',
            'role',
            'account_status',
            'creation_date',
            'last_login',
            'company_name',
            'company_address',
            'vat_tax_id',
            'industry',
            'company_size',
            'website',
        ]);
    });
}

};
