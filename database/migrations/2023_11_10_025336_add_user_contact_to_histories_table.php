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
        Schema::table('connection_histories', function (Blueprint $table) {
            $table->dropForeign(['connection_user_id']);

            $table->dropColumn('connection_user_id');

            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('contact_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('contact_id')->references('id')->on('contacts');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('connection_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['contact_id']);

            $table->dropColumn('user_id');
            $table->dropColumn('contact_id');

            $table->bigInteger('connection_user_id')->nullable();

            $table->foreign('connection_user_id')->references('id')->on('connection_users');
        });
    }

};
