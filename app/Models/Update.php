<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Update extends Eloquent {
    
    public function author() {
		return $this->belongsTo('App\Models\User', 'author_id');
	}
	
	public function tag() {
		return $this->belongsTo('App\Models\Tag');
	}
}