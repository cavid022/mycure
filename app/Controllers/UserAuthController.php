<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use Respect\Validation\Validator as vld;

class UserAuthController extends Controller {
    
    public function getLogout(Request $request, Response $response, $args) {
        $this->ci->auth->logout();
        return $response->withRedirect($this->ci->router->pathFor('auth.login'));
    }
    
    public function getLogin(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'auth_user/login.twig', [
            'title' => 'Giriş | MyCure',
        ]);
    }

    public function postLogin(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $auth = $this->ci->auth->attempt(
            $data['email'],
            $data['password']
        );
        if (!$auth) {
            $this->ci->flash->addMessage('error', 'Daxil etdiyiniz email və ya şifrə düzgün deyil');
            return $response->withRedirect($this->ci->router->pathFor('auth.login'));
        }
        $this->ci->flash->addMessage('info', 'You have logged in.');
        return $response->withRedirect($this->ci->router->pathFor('home.dashboard'));

    }
    
    public function getSignup(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'auth_user/signup_step_one.twig', [
            'title' => 'Qeydiyyat | MyCure',
        ]);
    }
   
    public function postSignup(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'first_name' => vld::noWhitespace()->notEmpty(),
            'last_name' => vld::noWhitespace()->notEmpty(),
            'email' => vld::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'password' => vld::alnum()->length(8, 30)->noWhitespace()->notEmpty(),
            'confirm_password' => vld::notEmpty()->confirmPassword($request->getParam('password'))
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('auth.signup.step_one'));
        }
        
        $data = $request->getParsedBody();
        $user = new User();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->save();
        $_SESSION['new_user'] = [
            'id' => $user->id,
            'password' => $data['password'],
        ];
        return $response->withRedirect($this->ci->router->pathFor('auth.signup.step_two'));
    }
    
    public function getSignupStepTwo(Request $request, Response $response, $args) {
        if (empty($_SESSION['new_user'])) {
            return $response->withRedirect($this->ci->router->pathFor('auth.signup.step_one'));
        }
        return $this->ci->view->render($response, 'auth_user/signup_step_two.twig', [
            'title' => 'Qeydiyyat | MyCure',
        ]);
    }
    
    public function postSignupStepTwo(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'username' => vld::noWhitespace()->notEmpty()->usernameAvailable(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('auth.signup.step_two'));
        }
        $user = User::find($_SESSION['new_user']['id']);
        $data = $request->getParsedBody();
        $user->phone = $data['phone'];
        $user->username = $data['username'];
        $user->gender = $data['gender'];
        $user->date_of_birth = $data['date_of_birth'];
        $user->nationality = $data['nationality'];
        $user->save();
        $auth = $this->ci->auth->attempt($user->email, $_SESSION['new_user']['password']);
        unset($_SESSION['new_user']);
        if (!$auth) {
            return $response->withRedirect($this->ci->router->pathFor('auth.login'));
        }
        $this->ci->flash->addMessage('info', 'You have signed up.');
        return $response->withRedirect($this->ci->router->pathFor('home.dashboard'));
    }
    
}