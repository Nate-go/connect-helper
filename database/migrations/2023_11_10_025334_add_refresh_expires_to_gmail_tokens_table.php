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
            $table->string('refresh_token')->nullable();
            $table->timestamp('expired_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('gmail_tokens', function (Blueprint $table) {
            $table->dropColumn('refresh_token');
            $table->dropColumn('expired_at');
        });
    }

};
