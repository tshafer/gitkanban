<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('source_providers', function (Blueprint $table) {
            $table->string('refresh_token')->nullable();
            $table->string('expires_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('source_providers', function (Blueprint $table) {
            $table->dropColumn('refresh_token');
            $table->dropColumn('expires_in');
        });
    }
};
