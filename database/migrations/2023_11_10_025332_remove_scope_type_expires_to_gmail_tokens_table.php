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
        Schema::table('gmail_tokens', function (Blueprint $table) {
            $table->dropColumn('scope');
            $table->dropColumn('token_type');
            $table->dropColumn('expires_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('scope')->nullable();
            $table->string('token_type')->nullable();
            $table->integer('expires_in')->nullable();
        });
    }

};
