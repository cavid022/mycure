<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIllnessesTable extends Migration {
    
    public function up() {
        $this->schema->create('illnesses', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
        
        $this->schema->table('illnesses', function(Blueprint $table) {
            //
        });
    }
    
    public function down() {
        $this->schema->drop('illnesses');
        
        $this->schema->create('illnesses', function(Blueprint $table) {
            //
        });
    }
    
}