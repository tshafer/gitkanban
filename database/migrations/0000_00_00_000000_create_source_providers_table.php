<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('source_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('token')->nullable();
            $table->string('label')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('type', 25)->nullable();
            $table->text('json')->nullable();
            $table->timestamps();
        });
    }
};
