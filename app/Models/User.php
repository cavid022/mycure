<?php namespace App\Models;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\Participant;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent {
	
	public function fullname() {
		return $this->first_name . " " . $this->last_name;
	}
    
    public function messages() {
		return $this->hasMany('App\Models\Message');
	}
	
	public function histories()	{
		return $this->hasMany('App\Models\History');
	}
	
	public function doctor() {
		return $this->hasOne('App\Models\Doctor');
	}
	
	public function department() {
		return $this->belongsTo('App\Models\Department');
	}
	
	public function conversations()	{
		return $this->belongsToMany('App\Models\Conversation', 'participants');
	}
	
	public function newMessagesCount() {
		return count($this->conversationsWithNewMessages());
	}
	
	public function conversationsWithNewMessages()
	{
		$conversationsWithNewMessages = [];
		$participants = Participant::where('user_id', $this->id)->lists('last_read', 'conversation_id');
		if ($participants)
		{
			$conversations = Conversation::whereIn('id', array_keys($participants))->get();
			foreach ($conversations as $conversation)
			{
				if ($conversation->updated_at > $participants[$conversation->id])
				{
					$conversationsWithNewMessages[] = $conversation->id;
				}
			}
		}
		return $conversationsWithNewMessages;
	}

	public function notifications(){
		return $this->hasMany('App\Models\Notification');	
	}
	
	public function not_read_notifications(){
		return $this->hasMany('App\Models\Notification')->where('is_read', 0)->get();	
	}
	
	public function updates() {
		return $this->hasMany('App\Models\Update');
	}
	
	public function results() {
		return $this->hasMany('App\Models\Result');
	}
	
	public function appointments(){
		return $this->hasMany('App\Models\Appointment');	
	}
	
	public function permitted_doctors() {
	    return $this->belongsToMany('App\Models\User', 'history_allowances', 'user_id', 'doctor_id');
	}
	
	public function accessible_users() {
	    return $this->belongsToMany('App\Models\User', 'history_allowances', 'doctor_id', 'user_id');
	}
	
}