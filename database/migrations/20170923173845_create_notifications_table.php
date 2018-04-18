<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {
    
   public function up() {
        $this->schema->create('notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('is_read'); 
            $table->string('first_name');
            $table->string('last_name');
            $table->string('title');
            $table->integer('type_id');
            $table->string('type');
            $table->timestamps();
        });
        
        $this->schema->table('notifications', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('notifications');
    }
    
}