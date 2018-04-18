<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\Illness;
use Respect\Validation\Validator as vld;

class IllnessesController extends Controller {
    
    public function getAllIllnesses(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $illnesses = Illness::all();
         
        return $this->ci->view->render($response, 'illnesses/main.twig', [
            'title' => 'Xəstəliklər | MyCure',
            'illnesses' =>  $illnesses,
            'page_id' => 'illnesses',
        ]);
    }
    
    public function getIllness(Request $request, Response $response, $args) {
        $illness_id = $request->getAttribute('id');
        if ($illness = Illness::where('id', $illness_id)->first()) {
            return $this->ci->view->render($response, 'illnesses/show.twig', [
                'title' => 'Xəstəliklər | MyCure',
                'illness' => $illness,
                'page_id' => 'illnesses',
            ]);
        } else {
            return "Illness not found.";
        }
    }
    
    public function getIllnessesByLetter(Request $request, Response $response, $args) {
        $letter = $request->getAttribute('letter');
        $illnesses = Illness::where('title', 'like', $letter."%")->get();
        return $this->ci->view->render($response, 'illnesses/by_letter.twig', [
            'title' => 'Xəstəliklər | MyCure',
            'letter' => $request->getAttribute('letter'),
            'illnesses' =>  $illnesses,
            'page_id' => 'illnesses',
        ]);
    }
    
}