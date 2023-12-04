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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('connection_id')->nullable();
            $table->string('content')->nullable();
            $table->integer('type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('connection_id')->references('id')->on('connections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['connection_id']);

        });
        Schema::dropIfExists('contacts');

        Schema::enableForeignKeyConstraints();
    }
};
