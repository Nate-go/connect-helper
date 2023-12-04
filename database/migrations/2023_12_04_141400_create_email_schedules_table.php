<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('send_mail_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('nextTime_at')->nullable();
            $table->unsignedBigInteger('after_second')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('send_mail_id')->references('id')->on('send_mails');
        });
    }

    public function down(): void
    {
        Schema::table('email_schedules', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['send_mail_id']);
        });
        Schema::dropIfExists('email_schedules');
    }
};
