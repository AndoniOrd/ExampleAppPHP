<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permission_user', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type')->default('App\Models\User'); // Polymorphic relationship
    
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
    
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
    
            $table->primary(['permission_id', 'user_id', 'user_type']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('permission_user');
    }
};
