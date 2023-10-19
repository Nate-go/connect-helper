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
        Schema::create('survey_element_votes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('survey_id')->nullable();
            $table->bigInteger('survey_element_id')->nullable();
            $table->bigInteger('contact_id')->nullable();
            $table->string('content')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('survey_id')->references('id')->on('surveys');
            $table->foreign('survey_element_id')->references('id')->on('survey_elements');
            $table->foreign('contact_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servey_element_votes');
    }
};
