<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\Update;

class MainController extends Controller {
    
    public function dashboard(Request $request, Response $response, $args) {
        $updates = Update::all();
        return $this->ci->view->render($response, 'home.twig', [
            'title' => 'Əsas səhifə | MyCure',
            'updates' =>  $updates,
        ]);
    }
    
    public function getAbout(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'about.twig', [
            'title' => 'Haqqımızda | MyCure',
            'page_id' => 'about',
        ]);
    }
    
    public function getContactUs(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'contact.twig', [
            'title' => 'Əlaqə | MyCure',
            'page_id' => 'contact',
        ]);
    }
    
}