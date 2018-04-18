<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUpdatesTable extends Migration {
    
    public function up() {
        $this->schema->create('updates', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned();
            $table->integer('tag_id')->unsigned()->nullable();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->integer('view_count')->unsigned();
            $table->string('language');
            $table->timestamps();
        });
        
        $this->schema->table('updates', function(Blueprint $table) {
            $table->foreign('author_id')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('updates');
    }
    
}