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
        Schema::create('connection_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('connection_id')->nullable();
            $table->bigInteger('tag_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('connection_id')->references('id')->on('connections');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('connection_tags', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
            $table->dropForeign(['connection_id']);

        });
        Schema::dropIfExists('connection_tags');

        Schema::enableForeignKeyConstraints();
    }
};
