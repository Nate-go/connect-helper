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
        Schema::table('send_mails', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->string('content', 1000)->nullable();
            $table->string('name')->nullable();
            $table->integer('type')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('send_mails', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->dropColumn('user_id');
            $table->dropColumn('title');
            $table->dropColumn('type');
            $table->dropColumn('content');
            $table->dropColumn('name');
        });
    }

};
