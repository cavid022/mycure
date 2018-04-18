<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Doctor extends Eloquent {
    
	public function user() {
		return $this->belongsTo('App\Models\User');
	}
	
}