<?php namespace App\Controllers\Admin;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

use App\Models\Result;
use App\Models\Notification;

class ResultsController extends Controller {
    
    public function getAllResults(Request $request, Response $response, $args) {
        $results = Result::paginate(10, ['*'], 'page', $request->getParam('page'));
        $results->setPath($this->ci->router->pathFor('admin.results.all'));
        return $this->ci->view->render($response, 'admin/results/all.twig', [
            'title' => 'Nəticələr',
            'results' => $results,
            'page_id' => 'results',
        ]);
    }
    
    public function postDeleteResult(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $result = Result::find($id);
        $notifications = Notification::where('type', 'result')->where('type_id', $result->id);
        $result->delete();
        $notifications->delete();
        return $response->withRedirect($this->ci->router->pathFor('admin.results.all'));
    }
    
}