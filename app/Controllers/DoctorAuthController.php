<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Department;
use App\Models\Hospital;
use Respect\Validation\Validator as vld;
use Slim\Http\UploadedFile;

class DoctorAuthController extends Controller {

    public function getSignup(Request $request, Response $response, $args) {
        $hospitals = Hospital::all();
        return $this->ci->view->render($response, 'auth_doctor/signup_step_one.twig', [
            'title' => 'Həkim Qeydiyyatı | MyCure',
            'hospitals' => $hospitals,
        ]);
    }
    
    public function postSignup(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'first_name' => vld::noWhitespace()->notEmpty(),
            'last_name' => vld::noWhitespace()->notEmpty(),
            'email' => vld::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'password' => vld::alnum()->length(8, 30)->noWhitespace()->notEmpty(),
            'confirm_password' => vld::notEmpty()->confirmPassword($request->getParam('password')),
            'assigned_id' => vld::notEmpty()->digit(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('doctor.auth.signup.step_one'));
        }
        
        $data = $request->getParsedBody();
        $user = new User();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->role = "doctor";
        $user->save();
        $department = Department::find($data['department_id']);
        $doctor = new Doctor();
        $doctor->assigned_id = $data['assigned_id'];
        $doctor->user_id = $user->id;
        $user->doctor()->save($doctor);
        $department->doctors()->save($user);
        $_SESSION['new_doctor'] = [
            'id' => $user->id,
            'password' => $data['password'],
        ];
        return $response->withRedirect($this->ci->router->pathFor('doctor.auth.signup.step_two'));
    }
    
    public function getSignupStepTwo(Request $request, Response $response, $args) {
        if (empty($_SESSION['new_doctor'])) {
            return $response->withRedirect($this->ci->router->pathFor('doctor.auth.signup.step_one'));
        }
        $hospitals = Hospital::all();
        return $this->ci->view->render($response, 'auth_doctor/signup_step_two.twig', [
            'title' => 'Həkim Qeydiyyatı | MyCure',
            'hospitals' => $hospitals,
        ]);
    }
    
    public function postSignupStepTwo(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'username'=> vld::noWhitespace()->notEmpty()->usernameAvailable(),
            'phone'=> vld::notEmpty(),
            // 'date_of_birth'=> vld::noWhitespace()->notEmpty(),
            'gender'=> vld::noWhitespace()->notEmpty(),
            'nationality'=> vld::noWhitespace()->notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('doctor.auth.signup.step_two'));
        }
        $data = $request->getParsedBody();
        $user = User::find($_SESSION['new_doctor']['id']);
        $user->username = $data['username'];
        $user->phone = $data['phone'];
        $user->gender = $data['gender'];
        $user->date_of_birth = $data['date_of_birth'];
        $user->nationality = $data['nationality'];
        // $directory = $this->ci->get('uploads_directory');
        // $uploadedFiles = $request->getUploadedFiles();
        // $uploadedFile = $uploadedFiles['example1'];
        // if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            // $filename = $this->moveUploadedFile($directory, $uploadedFile, $user->username);
            // $doctor->image = $filename;
        // }
        $user->save();
        return $response->withRedirect($this->ci->router->pathFor('doctor.auth.signup.step_three'));
    }
    
    public function getSignupStepThree(Request $request, Response $response, $args) {
        if (empty($_SESSION['new_doctor'])) {
            return $response->withRedirect($this->ci->router->pathFor('doctor.auth.signup.step_one'));
        }
        return $this->ci->view->render($response, 'auth_doctor/signup_step_three.twig', [
            'title' => 'Həkim Qeydiyyatı | MyCure',
        ]);
    }
    
    public function postSignupStepThree(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $user = User::find($_SESSION['new_doctor']['id']);
        $doctor = $user->doctor;
        
        $doctor->specialty = $data['specialty'];
        $doctor->education = $data['education'];
        $doctor->experience = $data['experience'];
        $doctor->scientific_articles = $data['scientific_articles'];
        $user->doctor()->save($doctor);
        $user->save();
        
        $auth = $this->ci->auth->attempt($user->email, $_SESSION['new_doctor']['password']);

        unset($_SESSION['new_doctor']);
        if (!$auth) {
            return $response->withRedirect($this->ci->router->pathFor('auth.login'));
        }
        $this->ci->flash->addMessage('info', 'You have signed up.');
        return $response->withRedirect($this->ci->router->pathFor('home.dashboard'));
    }
    
    public function postDepartmentsByHospitalAjax(Request $request, Response $response, $args)  {
        $data = $request->getParsedBody();
        if($data['query']) {
        $departments = Hospital::find($data['query'])->departments()->get();
            foreach($departments as $department) {
                echo "<option value='" . $department->id . "'>" . $department->name . "</option>";
            }
        }
    }
    
    function moveUploadedFile($directory, UploadedFile $uploadedFile, $prefix = "doctor") {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = $prefix . bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
    
}