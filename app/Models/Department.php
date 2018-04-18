<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Department extends Eloquent {
    
    public function doctors() {
        return $this->hasMany('App\Models\User');
    }
    
    public function hospital() {
		return $this->belongsTo('App\Models\Hospital');
	}
}