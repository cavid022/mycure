<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Result extends Eloquent {
	
	public function doctor() {
		return $this->belongsTo('App\Models\User', 'doctor_id', 'id');
	}
	
	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
    
}