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
        Schema::create('schedule_contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('schedule_id')->nullable();
            $table->bigInteger('contact_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('schedule_id')->references('id')->on('schedules');
            $table->foreign('contact_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_contacts', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropForeign(['contact_id']);        
        });
        Schema::dropIfExists('schedule_contacts');
    }
};
