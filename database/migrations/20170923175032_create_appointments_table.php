<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppointmentsTable extends Migration {
    
    public function up() {
        $this->schema->create('appointments', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('doctor_id')->unsigned();
            $table->string('date');
            $table->string('time');
            $table->timestamps();
        });
        
        $this->schema->table('appointments', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('appointments');
    }
    
}