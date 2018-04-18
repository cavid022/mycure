<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {
    
    public function up() {
        $this->schema->create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('role')->default('user');
            $table->string('password');
			$table->integer('department_id')->unsigned()->nullable();
            $table->timestamps();
        });
        
        $this->schema->table('users', function(Blueprint $table) {
        	$table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('users');
    }
    
}