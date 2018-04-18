<?php namespace App\Controllers\Admin;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

use App\Models\Appointment;
use App\Models\Notification;

class AppointmentsController extends Controller {
    
    public function getAllAppointments(Request $request, Response $response, $args) {
        $appointments = Appointment::paginate(10, ['*'], 'page', $request->getParam('page'));
        $appointments->setPath($this->ci->router->pathFor('admin.appointments.all'));
        return $this->ci->view->render($response, 'admin/appointments/all.twig', [
            'title' => 'Təyinatlar',
            'appointments' => $appointments,
            'page_id' => 'appointments',
        ]);
    }
    
    public function postDeleteAppointment(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $appointment = Appointment::find($id);
        $notifications = Notification::where('type', 'result')->where('type_id', $result->id);
        $appointment->delete();
        $notifications->delete();
        return $response->withRedirect($this->ci->router->pathFor('admin.appointments.all'));
    }
    
}