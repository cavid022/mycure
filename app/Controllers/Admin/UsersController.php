<?php namespace App\Controllers\Admin;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;
use App\Models\User;
use App\Models\Admin;
use App\Models\Update;

class UsersController extends Controller {
    
    
    public function getLogin(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'admin/auth_admin/login.twig', [
            'title' => 'Giriş',
        ]);
    }

    public function postLogin(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $admin_auth = $this->ci->admin_auth->attempt(
            $data['email'],
            $data['password']
        );
        
        if (!$admin_auth) {
            $this->ci->flash->addMessage('error', 'Daxil etdiyiniz email və ya şifrə düzgün deyil');
            return $response->withRedirect($this->ci->router->pathFor('admin.login'));
        }
        $cookie = $this->ci->remember_me->attempt(
            $data['email'],
            $data['password'],
            $data['remember_me']
        );

        $this->ci->flash->addMessage('info', 'You have logged in.');
        return $response->withRedirect($this->ci->router->pathFor('admin.updates.all'));

    }
    
    public function getUpdateUser(Request $request, Response $response, $args) {
        $users = User::find($request->getAttribute('id'));
        return $this->ci->view->render($response, 'admin/users/update.twig', [
            'title' => 'Xəstəliklər',
            'users' => $users,
            'page_id' => 'users',
        ]);
    }
    
    public function postUpdateUser(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'title' => vld::notEmpty(),
            'content' => vld::notEmpty(),
            'tag_id' => vld::notEmpty(),
            'language' => vld::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('admin.users.update', [
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
            $user = User::find($update_id);
            $user->title = $data['title'];
            $user->content = $data['content'];
            $user->image = $filename;
            $user->tag_id = $data['tag_id'];
            $user->language = $data['language'];
            $user->save();
        }

        return $response->withRedirect($this->ci->router->pathFor('admin.users.all'));
    }
    
    public function getLogout(Request $request, Response $response, $args) {
        $this->ci->admin_auth->logout();
        return $response->withRedirect($this->ci->router->pathFor('admin.login'));
    }
    
    public function getAllUsers(Request $request, Response $response, $args) {
        $users = User::paginate(10, ['*'], 'page', $request->getParam('page'));
        $users->setPath($this->ci->router->pathFor('admin.users.all'));
        return $this->ci->view->render($response, 'admin/users/all.twig', [
            'title' => 'İstifadəçilər',
            'users' => $users,
            'page_id' => 'users',
        ]);
    }
    
    public function getManageRoles(Request $request, Response $response, $args) {
        $users = User::paginate(10, ['*'], 'page', $request->getParam('page'));
        $users->setPath($this->ci->router->pathFor('admin.users.all'));
        return $this->ci->view->render($response, 'admin/users/roles.twig', [
            'title' => 'İstifadəçilər',
            'users' => $users,
            'page_id' => 'users',
        ]);
    }
    
    public function postDeleteUser(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $user = User::find($id);
        $user->delete();
        return $response->withRedirect($this->ci->router->pathFor('admin.users.all'));
    }
    
    public function getRoleUpdateUser(Request $request, Response $response, $args) {
        
        User::where('id', $request->getAttribute('id'))->update(['role' => 'admin']);
        
        return $response->withRedirect($this->ci->router->pathFor('admin.users.roles'));
    
    }
        

}