<?php namespace App\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Conversation;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class MessagesController extends Controller {
    
    /*
    * List of Users for starting new conversation
    */
    public function getUsersForNewConversations(Request $request, Response $response, $args)  {
        $data = $request->getParsedBody();
        $users = User::where('first_name', 'LIKE', '%'. $data['query'] . '%')
        ->orWhere('last_name', 'LIKE', '%'. $data['query'] . '%')
        ->orWhere('username', 'LIKE', '%'. $data['query'] . '%')
        ->orWhere('email', 'LIKE', $data['query'] . '%')
        ->get();
        foreach($users as $user) {
            if ($user != $this->ci->auth->user()) {
                $redirect = $this->ci->router->pathFor('conversations.create', [
                    'recipient' => $user->id,
                ]);
                echo "<li><a href='{$redirect}'>{$user->fullname()}</a></li>";
            }
        }
    }
    
    // public function index(Request $request, Response $response, $args) {
    //     $user = User::find($request->getAttribute('id'));
    //     $conversations = Conversation::forUser($user->id)->get();
    //     if ($conversations->isEmpty())
    //     {
    //         return var_dump($conversations);
    //         // return Redirect::route('conversations.create');
    //     }
    //     return var_dump($user->messages[0]->body);
    //     // return Redirect::route('conversations.show', [$conversations->last()->id]);
    // }
    
    public function createConversation(Request $request, Response $response, $args) {
        $sender = User::find($this->ci->auth->user()->id);
        $recipient = User::find($request->getAttribute('recipient'));
        $existing_conversation = Conversation::where('subject', "{$sender->fullname()}, {$recipient->fullname()}")->orWhere('subject', "{$recipient->fullname()}, {$sender->fullname()}")->first();
        if (isset($existing_conversation)) {
            return $response->withRedirect($this->ci->router->pathFor('conversations.show', [
            'id' => $existing_conversation->id,
        ]));
        } else {
            $conversation = Conversation::create([
                'subject' => "{$sender->fullname()}, {$recipient->fullname()}",
            ]);
            // Add Sender
            Participant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $sender->id,
            ]);
            // Add Recipient
            Participant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $recipient->id,
            ]);
            return $response->withRedirect($this->ci->router->pathFor('conversations.show', [
                'id' => $conversation->id,
            ]));
        }
    }
    
    public function getConversations(Request $request, Response $response, $args)  {
        $user = User::find($this->ci->auth->user()->id);
        $conversations = Conversation::forUser($user->id)->orderBy('updated_at', 'desc')->get();
        return $this->ci->view->render($response, 'conversations/all.twig', [
            'Mesajlar | MyCure' => 'Mycure',
            'conversations' => $conversations,
        ]);
    }
    
    public function showConversation(Request $request, Response $response, $args) {
        if ($conversation = Conversation::find($request->getAttribute('id'))) {
            if ($conversation->owner($this->ci->auth->user()->id)) {
                $user = User::find($this->ci->auth->user()->id);
                $conversations = Conversation::forUser($user->id)->orderBy('updated_at', 'desc')->get();
                $messages = $conversation->messages()->orderBy('updated_at')->get();
                return $this->ci->view->render($response, 'conversations/show.twig', [
                    'title' => 'Mesajlar | MyCure',
                    'messages' => $messages,
                    'conversation' => $conversation,
                    'conversations' => $conversations,
                ]);
            } else {
                return "You do not have access";
            }
        } else {
            return "Conversation not found";
        }
    }
    
    public function sendMessage(Request $request, Response $response, $args)  {
        $conversation = Conversation::find($request->getParam('conversation_id'));
        $message = new Message();
        $message->body = $request->getParam('message');
        $message->user()->associate($this->ci->auth->user());
        $message->conversation()->associate($conversation);
        // $conversation->messages()->save($message);
        $message->save();
        return $response->withRedirect($this->ci->router->pathFor('conversations.show', [
            'id' => $conversation->id,
        ]));
    }
    
    // public function store(Request $request, Response $response, $args)  {
    //     // $data = $request->getParsedBody();
    //     $conversation = Conversation::create([
    //         // 'subject' => $data['subject'],
    //         'subject' => "example subject",
    //     ]);
    //     $message = Message::create([
    //         'conversation_id' => $conversation->id,
    //         'user_id' => $this->ci->auth->user()->id,
    //         // 'body' => $input['message'],
    //         'body' => 'example long message',
    //     ]);
    //     $sender = Participant::create([
    //         'conversation_id' => $conversation->id,
    //         'user_id' => 1
    //     ]);
    //     // if ($this->input->has('recipient'))
    //     // {
    //         // $recipient = User::where('email', $input['recipient'])->first();
    //         $recipient = User::where('email','kenanasadov@bk.ru')->first();
    //         Participant::create([
    //             'conversation_id' => $conversation->id,
    //             'user_id' => $recipient->id,
    //         ]);
    //     // }
    //     return "Yes!";
    //     // return Redirect::route('conversations.index');
    // }
    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return Response
    //  */
    // public function createMessage($conversation)
    // {
    //     return View::make('conversations.create');
    // }
    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @return Response
    //  */
    // public function storeMessage($conversation)
    // {
    //     $message = Message::create([
    //         'conversation_id' => $conversation,
    //         'user_id' => $this->auth->user()->id,
    //         'body' => $this->input->input('message'),
    //     ]);
    //     return Redirect::route('conversations.show', $conversation);
    // }
    
}