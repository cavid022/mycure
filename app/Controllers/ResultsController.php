<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Result;
use App\Models\User;
use App\Models\Notification;
use Slim\Http\UploadedFile;
use Respect\Validation\Validator as vld;

class ResultsController extends Controller {

    public function getResults(Request $request, Response $response, $args) {
        if ($this->ci->auth->user()->role == "doctor") {
            $results = Result::where('doctor_id', $this->ci->auth->user()->id)->get();
        } else if ($this->ci->auth->user()->role == "user" || $this->ci->auth->user()->role == "admin") {
            $results = $this->ci->auth->user()->results;
        }
        return $this->ci->view->render($response, 'results/all.twig', [
            'title' => 'Analizlər | MyCure',
            'results' => $results,
            'tab_id' => 'results',
        ]);
    }
    
    public function getAddResult(Request $request, Response $response, $args) {
        $users = User::where('role', 'user')->orWhere('role', 'admin')->get();
        return $this->ci->view->render($response, 'results/add.twig', [
            'title' => 'Analizlər | MyCure',
            'users' => $users,
            'tab_id' => 'results',
        ]);
    }
    
    public function postResult(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $directory = $this->ci->get('uploads_directory');
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['result_file'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $result = new Result();
            $result->title = $data['title'];
            $result->user_id = $data['user_id'];
            $result->doctor_id = $this->ci->auth->user()->id;
            $result->file_name = $filename;
            $result->save();
            
            // Notification insert
            $notification = new Notification();
            $notification->user_id = $data['user_id'];
            $notification->first_name = $this->ci->auth->user()->first_name;
            $notification->last_name = $this->ci->auth->user()->last_name;
            $notification->type = 'result';
            $notification->title = "Laboratoriya analizi yükləndi";
            $notification->type_id = $result->id; 
            $notification->is_read = 0;
            $notification->save();
            
            $pusher = new \Pusher\Pusher(
                '31cd2feb6361964fd937',
                '200eb0474111c4433473',
                '385809',
                array(
                    'cluster' => 'ap2',
                    'encrypted' => true
                )
            );
            
            $data = [
                'message' => $notification
            ];
            $pusher->trigger($notification->user_id.'@notification', 'new_notification', $data);
            }
        return $response->withRedirect($this->ci->router->pathFor('results.all'));
    }
    
    public function postDeleteResult(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $result = Result::find($id);
        $notifications = Notification::where('type', 'result')->where('type_id', $result->id);
        $result->delete();
        $notifications->delete();
        return $response->withRedirect($this->ci->router->pathFor('results.all'));
    }
    
    function moveUploadedFile($directory, UploadedFile $uploadedFile) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . 'results'. DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
  
    
}