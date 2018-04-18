<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDepartmentsTable extends Migration {
    
    public function up() {
        $this->schema->create('departments', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
			$table->integer('hospital_id')->unsigned()->nullable();
            $table->timestamps();
        });
        
        $this->schema->table('departments', function(Blueprint $table) {
        	$table->foreign('hospital_id')->references('id')->on('hospitals')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('departments');
    }
    
}