<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurvivorFlagLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survivor_flag_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flag_origin')->constrained('survivors');
            $table->foreignId('flagged_survivor')->constrained('survivors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survivor_flag_logs');
    }
}
