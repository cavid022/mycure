<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use App\Models\Insurance;
use App\Models\Illness;
use Respect\Validation\Validator as vld;

class InsurancesController extends Controller {
    
    public function getInsurances(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'insurances/all.twig', [
            'title' => 'Sığorta | MyCure',
            'tab_id' => 'insurances',
        ]);
    }
    
    public function showInsurance(Request $request, Response $response, $args) {
        $type = $request->getAttribute('type');
        if ($insurance = Insurance::where('type',$type)->first()) {
            return $this->ci->view->render($response, 'insurances/show.twig', [
                'title' => 'Sığorta | MyCure',
                'insurance' => $insurance,
                'tab_id' => 'insurances',
            ]);
        } else {
            return "Insurance not found.";
        }
    }
    
    public function getPayment(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'insurances/payment.twig', [
            'title' => 'Ödəmə | MyCure',
            'tab_id' => 'insurances',
        ]);
    }
    
}