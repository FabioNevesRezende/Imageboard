<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anaos', function (Blueprint $table) {
            $table->string('biscoito', 128);
            $table->string('ip', 15);
            $table->string('user_agent', 1024);
            $table->char('countrycode', 2)->nullable();
            $table->timestamps();
            $table->primary('biscoito');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anaos');
    }
}
