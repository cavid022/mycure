<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoriesTable extends Migration {
    
    public function up() {
        $this->schema->create('histories', function(Blueprint $table) {
            $table->increments('id');
        	$table->integer('user_id')->unsigned();
			$table->integer('doctor_id')->unsigned();
			$table->integer('illness_id')->unsigned();
			$table->text('simptoms');
			$table->text('details');
			$table->date('start_date');
			$table->date('end_date');
			$table->timestamps();
        });
        
        $this->schema->table('histories', function(Blueprint $table) {
        	$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        	$table->foreign('doctor_id')->references('id')->on('users')->onUpdate('cascade');
        	$table->foreign('illness_id')->references('id')->on('illnesses')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('histories');
    }
    
}