<?php namespace App\Controllers\Admin;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;
use App\Models\Insurance;
use App\Models\Update;
use Respect\Validation\Validator as vld;

class InsurancesController extends Controller {
    
    public function getAllInsurances(Request $request, Response $response, $args) {
        $insurances = Insurance::paginate(10, ['*'], 'page', $request->getParam('page'));
        $insurances->setPath($this->ci->router->pathFor('admin.insurances.all'));
        return $this->ci->view->render($response, 'admin/insurances/all.twig', [
            'title' => 'Sığorta',
            'insurances' => $insurances,
            'page_id' => 'insurances',
        ]);
    }
    
    public function getAddInsurances(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'admin/insurances/add.twig', [
            'title' => 'Sığorta',
            'page_id' => 'insurances',
        ]);
    }
    
    public function postAddInsurances(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $insurance = new Insurance();
        $insurance->title = $data['title'];
        $insurance->type = $data['type'];
        $insurance->company = $data['company'];
        $insurance->content = $data['content'];
        $insurance->price = $data['price'];
        $insurance->currency = $data['currency'];
        $insurance->save();
        return $response->withRedirect($this->ci->router->pathFor('admin.insurances.all'));
    }
    
    public function postDeleteInsurance(Request $request, Response $response, $args) {
        $id = $request->getAttribute('id');
        $insurance = Insurance::find($id);
        $insurance->delete();
        return $response->withRedirect($this->ci->router->pathFor('admin.insurances.all'));
    }
    
     public function getUpdateInsurance(Request $request, Response $response, $args) {
        $insurance = Insurance::find($request->getAttribute('id'));
        return $this->ci->view->render($response, 'admin/insurances/update.twig', [
            'title' => 'Xəstəliklər',
            'insurance' => $insurance,
            'page_id' => 'illnesses',
        ]);
    }
    
    public function postUpdateInsurance(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'title' => vld::notEmpty(),
            'content' => vld::notEmpty(),
            'company' => vld::notEmpty(),
            'type' => vld::notEmpty(),
            'price' => vld::notEmpty(),
            'currency' => vld::notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('admin.insurances.update', [
                'id' => $request->getAttribute('id'),
            ]));
        }
        
            $data = $request->getParsedBody();
            $update_id = $request->getAttribute('id');
            $insurance = Insurance::find($update_id);
            $insurance->title = $data['title'];
            $insurance->content = $data['content'];
            $insurance->company = $data['company'];
            $insurance->type = $data['type'];
            $insurance->price = $data['price'];
            $insurance->currency = $data['currency'];
            $insurance->save();

        return $response->withRedirect($this->ci->router->pathFor('admin.insurances.all'));
    
}

}