<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Hospital extends Eloquent {

    public function departments() {
        return $this->hasMany('App\Models\Department');
    }
}