<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminsTable extends Migration {
    
    public function up() {
        $this->schema->create('admins', function(Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->timestamps();
        });
        
        $this->schema->table('admins', function(Blueprint $table) {
            //
        });
    }
    
    public function down() {
        $this->schema->drop('admins');
        
    }
    
}