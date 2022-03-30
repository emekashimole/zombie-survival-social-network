<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurvivorInfectionFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survivor_infection_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survivor_id')->constrained();
            $table->integer('count')->default(1);
            $table->point('last_location')->nullable();
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
        Schema::dropIfExists('survivor_infection_flags');
    }
}
