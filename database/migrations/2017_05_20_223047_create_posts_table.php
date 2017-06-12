<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('assunto', 256); 
            $table->string('board', 10); 
            $table->char('modpost', 1)->nullable(); 
            $table->text('conteudo');
            $table->char('sage', 1);
            $table->char('pinado', 1);
            $table->integer('lead_id')->unsigned()->nullable();
            $table->string('ipposter', 15);
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
        Schema::dropIfExists('posts');
    }
}
