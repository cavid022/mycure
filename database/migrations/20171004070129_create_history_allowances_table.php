<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoryAllowancesTable extends Migration {
    
    public function up() {
        $this->schema->create('history_allowances', function(Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('doctor_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
        
        $this->schema->table('history_allowances', function(Blueprint $table) {
            //
        });
    }
    
    public function down() {
        $this->schema->drop('history_allowances');
    }
    
}