<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResultsTable extends Migration {
    
    public function up() {
        $this->schema->create('results', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('user_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->string('file_name');
            $table->timestamps();
        });
        
        $this->schema->table('results', function(Blueprint $table) {
            $table->foreign('doctor_id')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('results');
    }
    
}