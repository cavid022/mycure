<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsTable extends Migration {
    
    public function up() {
        $this->schema->create('tags', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });
    }
    
    public function down() {
        $this->schema->drop('tags');
    }
    
}