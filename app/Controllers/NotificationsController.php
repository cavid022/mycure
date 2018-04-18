<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Notification;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Result;

class NotificationsController extends Controller {
        
    public function getNotification(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        if ($notification = Notification::where('id', $id)->first()) {
            if ($notification->type == "appointment") {
                $requested_object = Appointment::where('id', $notification->type_id)->first();
                $object_type = "appointment";
            } else if ($notification->type == "result") {
                $requested_object = Result::where('id', $notification->type_id)->first();
                $object_type = "result";
            }
            $notification->is_read = 1;
            $notification->save();
            return $this->ci->view->render($response, 'notifications/show.twig', [
                'title' => 'Bildirişlər | MyCure',
                'requested_object' => $requested_object,
                'object_type' => $object_type,
            ]);
        } else {
            return "Notification not found.";
        }
    }
    
    public function getNotifications(Request $request, Response $response, $args) {
        $type_id = $request->getAttribute('type_id');
        $type = Notification::where('type_id',$type_id)->value('type');
        $inners = $this->ci->type->check($type_id,$type);
        return $this->ci->view->render($response, 'notifications/is_read.twig', [
            'title' => 'Bildirişlər | MyCure',
            'inners' => $inners,
            'type' => $type,
        ]);
    }
}

?>