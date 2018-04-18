<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHospitalsTable extends Migration {
    
    public function up() {
        $this->schema->create('hospitals', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('website')->nullable();
            $table->timestamps();
        });
        
        // $this->schema->table('hospitals', function(Blueprint $table) {
        //     //
        // });
    }
    
    public function down() {
        $this->schema->drop('hospitals');
    }
    
}