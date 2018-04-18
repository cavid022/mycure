<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDoctorsTable extends Migration {
    
    public function up() {
        $this->schema->create('doctors', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('assigned_id')->unique();
            $table->string('specialty')->nullable();
            $table->string('education')->nullable();
            $table->string('experience')->nullable();
            $table->string('scientific_articles')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
        
       $this->schema->table('doctors', function(Blueprint $table) {
          	$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('doctors');
    }
    
}