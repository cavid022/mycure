<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsurancesTable extends Migration {
    
    public function up() {
        $this->schema->create('insurances', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('company');
            $table->string('type');
            $table->string('content');
            $table->integer('price');
            $table->smallInteger('currency');
            $table->timestamps();
        });
        
        $this->schema->table('insurances', function(Blueprint $table) {
            //
        });
    }
    
    public function down() {
        $this->schema->drop('insurances');
    }
    
}