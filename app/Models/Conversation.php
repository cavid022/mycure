<?php

namespace App\Models;

use App\Models\User;
use App\Models\Message;
use App\Models\Participant;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Conversation extends Eloquent {

    protected $fillable = ['subject'];
    
    public function owner($user_id) {
    	$participants = $this->participants;
    	foreach($participants as $participant) {
			if ($participant['user_id'] == $user_id) return true;
    	}
    	return false;
    }
    
    public function lastMessage() {
    	return $this->messages()->orderBy('created_at', 'desc')->first()->body;
    }
    
    public function lastMessageDate() {
    	return $this->messages()->orderBy('created_at', 'desc')->first()->updated_at;
    }
    
    public function heading() {
    	$users = explode(', ', $this->subject);
    	if (isset($_SESSION['user'])) {
	    	$user = User::find($_SESSION['user']);
	    	$index = array_search($user->fullname(), $users);
	    	unset($users[$index]);
	    	$heading = implode(", ", $users);
    	}
    	if (isset($_SESSION['doctor'])) {
	    	$user = User::find($_SESSION['doctor']);
	    	$index = array_search($user->fullname(), $users);
	    	unset($users[$index]);
	    	$heading = implode(", ", $users);
    	}
    	return $heading;
    }

    public function messages() {
		return $this->hasMany('App\Models\Message');
	}
	
	public function participants() {
		return $this->hasMany('App\Models\Participant');
	}
	
	public function scopeForUser($query, $user) {
		return $query->join('participants', 'conversations.id', '=', 'participants.conversation_id')
		->where('participants.user_id', $user)
		->select('conversations.*');
	}
	
	public function scopeWithNewMessages($query, $user) {
		return $query->join('participants', 'conversations.id', '=', 'participants.conversation_id')
		->where('participants.user_id', $user)
		->where('conversations.updated_at', '>', DB::raw('participants.last_read'))
		->select('conversations.*');
	}
	
	public function participantsString($user) {
		$participantNames = DB::table('users')
		->join('participants', 'users.id', '=', 'participants.user_id')
		->where('users.id', '!=', $user)
		->where('participants.conversation_id', $this->id)
		->select(DB::raw("concat(users.first_name, ' ', users.last_name) as name"))
		->lists('users.name');
		return implode(', ', $participantNames);
	}

	public function addParticipants(array $participants) {
		$userModel = new User;
		$participant_ids = [];
		if (is_array($participants)) {
			if (is_numeric($participants[0])) {
				$participant_ids = $participants;
			} else {
				$participant_ids = $userModel->whereIn('email', $participants)->lists('id');
			}
		} else {
			if (is_numeric($participants)) {
				$participant_ids = [$participants];
			} else {
				$participant_ids = $userModel->where('email', $participants)->lists('id');
			}
		} 
		if(count($participant_ids)) {
			foreach ($participant_ids as $user_id) {
				Participant::create([
					'user_id' => $user_id,
					'conversation_id' => $this->id,
				]);
			}
		}
	}
}