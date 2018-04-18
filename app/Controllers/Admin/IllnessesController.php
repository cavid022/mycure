<?php namespace App\Controllers\Admin;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;
use App\Models\Illness;
use App\Models\Update;
use Respect\Validation\Validator as vld;

class IllnessesController extends Controller {
    
    public function getAllIllnesses(Request $request, Response $response, $args) {
        $illnesses = Illness::paginate(2, ['*'], 'page', $request->getParam('page'));
        $illnesses->setPath($this->ci->router->pathFor('admin.illnesses.all'));
        return $this->ci->view->render($response, 'admin/illnesses/all.twig', [
            'title' => 'Xəstəliklər',
            'illnesses' => $illnesses,
            'page_id' => 'illnesses',
        ]);
    }
    
    public function getUpdateIllnesses(Request $request, Response $response, $args) {
        $illnesses = Illness::find($request->getAttribute('id'));
        return $this->ci->view->render($response, 'admin/illnesses/update.twig', [
            'title' => 'Xəstəliklər',
            'illnesses' => $illnesses,
            'page_id' => 'illnesses',
        ]);
    }
    
    public function postUpdateIllnesses(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'title' => vld::notEmpty(),
            'content' => vld::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('admin.illnesses.update', [
                'id' => $request->getAttribute('id'),
            ]));
        }
        
        $directory = $this->ci->get('uploads_directory');
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['image'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $data = $request->getParsedBody();
            $update_id = $request->getAttribute('id');
            $illness = Illness::find($update_id);
            $illness->title = $data['title'];
            $illness->content = $data['content'];
            $illness->image = $filename;
            $illness->save();
        }

        return $response->withRedirect($this->ci->router->pathFor('admin.illnesses.all'));
    }
    
    public function getAddIllnesses(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'admin/illnesses/add.twig', [
            'title' => 'Xəstəliklər',
            'page_id' => 'illnesses',
        ]);
    }
    
    public function postAddIllnesses(Request $request, Response $response, $args) {
        $directory = $this->ci->get('uploads_directory');
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['image'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $data = $request->getParsedBody();
            $illness = new Illness();
            $illness->title = $data['title'];
            $illness->content = $data['content'];
            $illness->image = $filename;
            $illness->save();
        }
        return $response->withRedirect($this->ci->router->pathFor('admin.illnesses.all'));
    }
    
    public function postDeleteIllness(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $history = Illness::find($id);
        $history->delete();
        return $response->withRedirect($this->ci->router->pathFor('admin.illnesses.all'));
    }
    
    
    function moveUploadedFile($directory, UploadedFile $uploadedFile) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . 'illnesses' . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}