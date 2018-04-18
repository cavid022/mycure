<?php namespace App\Controllers\Admin;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;
use App\Models\Update;
use App\Models\Tag;
use Respect\Validation\Validator as vld;

class UpdatesController extends Controller {
    
    public function getAllUpdates(Request $request, Response $response, $args) {
        $updates = Update::paginate(10, ['*'], 'page', $request->getParam('page'));
        $updates->setPath($this->ci->router->pathFor('admin.updates.all'));
        return $this->ci->view->render($response, 'admin/updates/all.twig', [
            'title' => 'Xəbərlər',
            'updates' => $updates,
            'page_id' => 'updates',
        ]);
    }
    
    public function getAddUpdates(Request $request, Response $response, $args) {
        $tags = Tag::all();
        return $this->ci->view->render($response, 'admin/updates/add.twig', [
            'title' => 'Xəbərlər',
            'page_id' => 'updates',
            'tags' => $tags,
        ]);
    }
    
     public function getUpdateUpdate(Request $request, Response $response, $args) {
        $tags = Tag::all();
        $updates = Update::find($request->getAttribute('id'));
        return $this->ci->view->render($response, 'admin/updates/update.twig', [
            'title' => 'Xəstəliklər',
            'updates' => $updates,
            'page_id' => 'illnesses',
            'tags' => $tags,
        ]);
    }
    
    public function postUpdateUpdate(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'title' => vld::notEmpty(),
            'content' => vld::notEmpty(),
            'tag_id' => vld::notEmpty(),
            'language' => vld::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('admin.updates.update', [
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
            $update = Update::find($update_id);
            $update->title = $data['title'];
            $update->content = $data['content'];
            $update->image = $filename;
            $update->tag_id = $data['tag_id'];
            $update->language = $data['language'];
            $update->save();
        }

        return $response->withRedirect($this->ci->router->pathFor('admin.updates.all'));
    }
    
    
    
    public function postAddUpdates(Request $request, Response $response, $args) {
        $directory = $this->ci->get('uploads_directory');
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['image'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $data = $request->getParsedBody();
            $update = new Update();
            $update->title = $data['title'];
            $update->content = $data['content'];
            $update->tag_id = $data['tag_id'];
            $update->author_id = $this->ci->admin_auth->admin()->id;
            $update->language = $data['language'];
            $update->image = $filename;
            $update->save();
        }
        return $response->withRedirect($this->ci->router->pathFor('admin.updates.all'));
    }
    
    public function postDeleteUpdate(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $history = Update::find($id);
        $history->delete();
        return $response->withRedirect($this->ci->router->pathFor('admin.updates.all'));
    }
    
    
    function moveUploadedFile($directory, UploadedFile $uploadedFile) {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . 'updates' . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}