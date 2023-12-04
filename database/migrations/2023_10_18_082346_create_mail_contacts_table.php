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
        Schema::create('send_mail_contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('send_mail_id')->nullable();
            $table->bigInteger('contact_id')->nullable();
            $table->string('title')->nullable();
            $table->string('content', 1000)->nullable();
            $table->integer('type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('send_mail_id')->references('id')->on('send_mails');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('send_mail_contacts', function (Blueprint $table) {
            $table->dropForeign(['send_mail_id']);

        });
        Schema::dropIfExists('send_mail_contacts');
    }
};
