<?php namespace App\Controllers\Admin;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

use App\Models\History;
use App\Models\Notification;

class HistoriesController extends Controller {
    
    public function getAllHistories(Request $request, Response $response, $args) {
        $histories = History::paginate(10, ['*'], 'page', $request->getParam('page'));
        $histories->setPath($this->ci->router->pathFor('admin.histories.all'));
        return $this->ci->view->render($response, 'admin/histories/all.twig', [
            'title' => 'Tarixçə',
            'histories' => $histories,
            'page_id' => 'histories',
        ]);
    }
    
    public function postDeleteHistory(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $history = History::find($id);
        $history->delete();
        return $response->withRedirect($this->ci->router->pathFor('admin.histories.all'));
    }
    
}