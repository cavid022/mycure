<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Notification extends Eloquent {
    
	public function fullname() {
	    
		return $this->first_name . " " . $this->last_name;
		
	}
    
}