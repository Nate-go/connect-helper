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
        Schema::create('connection_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('connection_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('connection_id')->references('id')->on('connections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connection_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['connection_id']);

        });
        Schema::dropIfExists('connection_users');
    }
};
