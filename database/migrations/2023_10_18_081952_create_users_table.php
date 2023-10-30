<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('enterprise_id')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password');
            $table->integer('role');
            $table->string('image_url')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('verify_code')->nullable();
            $table->timestamp('overtimed_at')->nullable();
            $table->integer('status')->nullable();
            $table->integer('gender')->nullable();
            $table->rememberToken();
            $table->timestamp('date_of_birth')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('enterprise_id')->references('id')->on('enterprises');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
