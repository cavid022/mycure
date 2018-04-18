<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Tag extends Eloquent {
    
    public function updates() {
		return $this->belongsTo('App\Models\Update');
	}
	
	public function title() {
		return $this->title;
	}

    
}