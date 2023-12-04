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
        Schema::create('send_mails', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_contact_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('from_contact_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('send_mails', function (Blueprint $table) {
            $table->dropForeign(['from_contact_id']);
        });
        Schema::dropIfExists('send_mails');
    }
};
