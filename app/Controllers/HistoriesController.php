<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\History;
use App\Models\Illness;
use Respect\Validation\Validator as vld;

class HistoriesController extends Controller {
    
    public function getHistories(Request $request, Response $response, $args) {
        if ($this->ci->auth->user()->role == "doctor") {
            $accessible_users = $this->ci->auth->user()->accessible_users;
            return $this->ci->view->render($response, 'histories/all.twig', [
                'title' => 'Tarixçə | MyCure',
                'accessible_users' => $accessible_users,
                'tab_id' => 'histories',
            ]);
        } else if ($this->ci->auth->user()->role == "user" || $this->ci->auth->user()->role == "admin") {
            $histories = $this->ci->auth->user()->histories()->get();
            return $this->ci->view->render($response, 'histories/all.twig', [
                'title' => 'Mənim Tarixçəm | MyCure',
                'histories' => $histories,
                'tab_id' => 'histories',
            ]);
        }
    }
    
    public function getPermittedDoctors(Request $request, Response $response, $args) {
        if ($this->ci->auth->user()->role == "user" || $this->ci->auth->user()->role == "admin") {
            $permitted_doctors = $this->ci->auth->user()->permitted_doctors;
            return $this->ci->view->render($response, 'histories/permitted_doctors.twig', [
                'title' => 'Mənim Tarixçəm | MyCure',
                'permitted_doctors' => $permitted_doctors,
                'tab_id' => 'histories',
            ]);
        } else if ($this->ci->auth->user()->role == "doctor") {
            return "You are not authorized to view this page";
        }
    }
    
    public function getUserHistories(Request $request, Response $response, $args) {
        if ($this->ci->auth->user()->role == "doctor") {
            $histories = History::where('user_id', $request->getAttribute('id'))->get();
            return $this->ci->view->render($response, 'histories/user_histories.twig', [
                'title' => 'Tarixçə | MyCure',
                'histories' => $histories,
                'tab_id' => 'histories',
            ]);
        } else if ($this->ci->auth->user()->role == "user" || $this->ci->auth->user()->role == "admin") {
            return "You are not authorized to view this page";
        }
    }
    
    public function getAddHistory(Request $request, Response $response, $args) {
        $doctors = User::where('role', 'doctor')->get();
        $illnesses = Illness::all();
        return $this->ci->view->render($response, 'histories/add.twig', [
            'title' => 'Mənim Tarixçəm | MyCure',
            'doctors' => $doctors,
            'illnesses' => $illnesses,
            'tab_id' => 'histories',
        ]);
    }
    
    public function postAddHistory(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'doctor_id' => vld::notEmpty(),
            'illness_id' => vld::notEmpty(),
            'simptoms' => vld::notEmpty(),
            'details' => vld::notEmpty(),
            'start_day' => vld::notEmpty(),
            'start_month' => vld::notEmpty(),
            'start_year' => vld::notEmpty(),
            'end_day' => vld::notEmpty(),
            'end_month' => vld::notEmpty(),
            'end_year' => vld::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('histories.add'));
        }
        
        $data = $request->getParsedBody();
        $history = new History();
        $history->user_id = $this->ci->auth->user()->id;
        $history->doctor_id = $data['doctor_id'];
        $history->illness_id = $data['illness_id'];
        $history->simptoms = $data['simptoms'];
        $history->details = $data['details'];
        $history->start_date = date_create_from_format('d-m-Y', $data['start_day'] . '-' . $data['start_month'] . '-' . $data['start_year']);
        $history->end_date = date_create_from_format('d-m-Y', $data['end_day'] . '-' . $data['end_month'] . '-' . $data['end_year']);
        $history->save();

        return $response->withRedirect($this->ci->router->pathFor('histories.all'));
    }
    
    public function postDeleteHistory(Request $request, Response $response, $args) {
        $delete_id = $request->getAttribute('id');
        $history = History::find($delete_id);
        $history->delete();
        
        return $response->withRedirect($this->ci->router->pathFor('histories.all'));
    }
    
    public function getUpdateHistory(Request $request, Response $response, $args) {
        $doctors = User::where('role', 'doctor')->get();
        $illnesses = Illness::all();
        $history = History::find($request->getAttribute('id'));
        
        return $this->ci->view->render($response, 'histories/update.twig', [
            'title' => 'Mənim Tarixçəm | MyCure',
            'doctors' => $doctors,
            'illnesses' => $illnesses,
            'history' => $history,
            'tab_id' => 'histories',
        ]);
    }
    
    public function postUpdateHistory(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'doctor_id' => vld::notEmpty(),
            'illness_id' => vld::notEmpty(),
            'simptoms' => vld::notEmpty(),
            'details' => vld::notEmpty(),
            'start_day' => vld::notEmpty(),
            'start_month' => vld::notEmpty(),
            'start_year' => vld::notEmpty(),
            'end_day' => vld::notEmpty(),
            'end_month' => vld::notEmpty(),
            'end_year' => vld::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('histories.update', [
                'id' => $request->getAttribute('id'),
            ]));
        }
        
        $data = $request->getParsedBody();
        $update_id = $request->getAttribute('id');
        $history = History::find($update_id);
        $history->user_id = $this->ci->auth->user()->id;
        $history->doctor_id = $data['doctor_id'];
        $history->illness_id = $data['illness_id'];
        $history->simptoms = $data['simptoms'];
        $history->details = $data['details'];
        $history->start_date = date_create_from_format('d-m-Y', $data['start_day'] . '-' . $data['start_month'] . '-' . $data['start_year']);
        $history->end_date = date_create_from_format('d-m-Y', $data['end_day'] . '-' . $data['end_month'] . '-' . $data['end_year']);
        $history->save();

        return $response->withRedirect($this->ci->router->pathFor('histories.all'));
    }
    
    
    public function getDoctorsAvailableForPermission(Request $request, Response $response, $args) {
        $key = $request->getParam('query');
        if ($key) {
            $doctors = User::where('first_name', 'LIKE', '%'.$key.'%')->where('role', '=', 'doctor')->orWhere('last_name', 'LIKE', '%'.$key.'%')->where('role', '=', 'doctor')->get();
        } else {
            $doctors = User::where('role', '=', 'doctor')->get();
        }
        $existing_doctors = $this->ci->auth->user()->permitted_doctors->pluck('id')->all();
        $doctors = $doctors->filter(function($doctor) use($existing_doctors) {
            return !in_array($doctor->id, $existing_doctors);
        });
        // var_dump($doctors);
        // die();
        return $this->ci->view->render($response, 'histories/ajax/doctors_available_for_permission.twig', [
            'doctors' => $doctors,
        ]);
    }
    
    
    
    // End of Histories Controller
}