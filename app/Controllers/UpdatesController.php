<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\Update;
use Respect\Validation\Validator as vld;

class UpdatesController extends Controller {
    
     public function getAllUpdates(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $updates = Update::all();
         
        return $this->ci->view->render($response, 'updates/all.twig', [
            'title' => 'Xəbərlər | MyCure',
            'updates' =>  $updates,
            'page_id' => 'updates',
        ]);
    }
    
    public function getUpdate(Request $request, Response $response, $args) {
        $update_id = $request->getAttribute('id');
        if ($update = Update::where('id', $update_id)->first()) {
            return $this->ci->view->render($response, 'updates/show.twig', [
                'title' => 'Xəbərlər | MyCure',
                'update' => $update,
                'page_id' => 'updates',
            ]);
        } else {
            return "Update not found.";
        }
    }
    
}