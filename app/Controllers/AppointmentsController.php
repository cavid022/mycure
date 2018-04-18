<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Illness;
use App\Models\Notification;
use Respect\Validation\Validator as vld;

class AppointmentsController extends Controller {
    
    public function getAppointments(Request $request, Response $response, $args) {
         if ($this->ci->auth->user()->role == "doctor") {
            $appointments = Appointment::where('doctor_id', $this->ci->auth->user()->id)->get();
        } else if ($this->ci->auth->user()->role == "user" || $this->ci->auth->user()->role == "admin") {
            $appointments = Appointment::where('user_id', $this->ci->auth->user()->id)->get();
        }
        return $this->ci->view->render($response, 'appointments/all.twig', [
            'title' => 'Təyinatlar | MyCure',
            'appointments' => $appointments,
            'tab_id' => 'appointments',
        ]);
    }
    
    public function getAddAppointments(Request $request, Response $response, $args) {
        $doctors = User::where('role','doctor')->get();
        return $this->ci->view->render($response, 'appointments/add.twig', [
            'title' => 'Təyinatlar | MyCure',
            'doctors' => $doctors,
            'tab_id' => 'appointments',
        ]);
    }
        
    public function postAppointment(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $appointment = new Appointment();
        $appointment->user_id = $this->ci->auth->user()->id;
        $appointment->doctor_id = $data['doctor_id'];
        $appointment->date = $data['date'];
        $appointment->time = $data['time'];
        $appointment->save();
        
        // Notification insert
        $notification = new Notification();
        $notification->user_id = $data['doctor_id'];
        $notification->first_name = $this->ci->auth->user()->first_name;
        $notification->last_name = $this->ci->auth->user()->last_name;
        $notification->type = 'appointment';
        $notification->title = "randevu aldı";
        $notification->type_id = $appointment->id; 
        $notification->is_read = 0;
        $notification->save();
        
        return $response->withRedirect($this->ci->router->pathFor('appointments.add'));
     }
 
    
}