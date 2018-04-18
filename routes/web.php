<?php
use App\Controllers\MainController;
use App\Controllers\UserAuthController;
use App\Controllers\UpdatesController;
use App\Controllers\IllnessesController;
use App\Controllers\DoctorAuthController;
use App\Controllers\SearchController;
use App\Controllers\MessagesController;
use App\Controllers\HistoriesController;
use App\Controllers\ResultsController;
use App\Controllers\InsurancesController;
use App\Controllers\AppointmentsController;
use App\Controllers\NotificationsController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\DoctorMiddleware;
use App\Middleware\AdminAuthMiddleware;
use App\Middleware\AdminGuestMiddleware;

use App\Controllers\Admin\MainController as MainAdminController;
use App\Controllers\Admin\IllnessesController as IllnessesAdminController;
use App\Controllers\Admin\UpdatesController as UpdatesAdminController;
use App\Controllers\Admin\AppointmentsController as AppointmentsAdminController;
use App\Controllers\Admin\ResultsController as ResultsAdminController;
use App\Controllers\Admin\HistoriesController as HistoriesAdminController;
use App\Controllers\Admin\InsurancesController as InsurancesAdminController;
use App\Controllers\Admin\UsersController as UsersAdminController;


/* 
*   This group is designated for Users who are not logged in 
*/
$app->group('', function() use ($app) {
    $app->get('/', UserAuthController::class . ':getLogin');
    $app->get('/login', UserAuthController::class . ':getLogin')
        ->setName('auth.login');
    $app->post('/login', UserAuthController::class . ':postLogin')
        ->setName('auth.post.login');
    $app->get('/signup', UserAuthController::class . ':getSignup')
        ->setName('auth.signup.step_one');
    $app->post('/signup', UserAuthController::class . ':postSignup')
        ->setName('auth.signup.step_one.post');
    $app->get('/signup/step_two', UserAuthController::class . ':getSignupStepTwo')
        ->setName('auth.signup.step_two');
    $app->post('/signup/step_two', UserAuthController::class . ':postSignupStepTwo')
        ->setName('auth.signup.step_two.post');
})->add(new GuestMiddleware($container));

/* 
*   This group is designated for Doctors who are not logged in 
*/

$app->group('/doctor', function() use ($app) {
    $app->get('/signup', DoctorAuthController::class . ':getSignup')->setName('doctor.auth.signup.step_one');
    $app->post('/signup', DoctorAuthController::class . ':postSignup')->setName('doctor.auth.signup.step_one.post');
    $app->get('/signup/step_two', DoctorAuthController::class . ':getSignupStepTwo')->setName('doctor.auth.signup.step_two');
    $app->post('/signup/step_two', DoctorAuthController::class . ':postSignupStepTwo')->setName('doctor.auth.signup.step_two.post');
    $app->get('/signup/step_three', DoctorAuthController::class . ':getSignupStepThree')->setName('doctor.auth.signup.step_three');
    $app->post('/signup/step_three', DoctorAuthController::class . ':postSignupStepThree')->setName('doctor.auth.signup.step_three.post');
    $app->get('/signup/step_four', DoctorAuthController::class . ':getSignupStepFour')->setName('doctor.auth.signup.step_four');
    $app->post('/signup/step_four', DoctorAuthController::class . ':postSignupStepFour')->setName('doctor.auth.signup.step_four.post');
    $app->post('/ajax/departments_by_name', DoctorAuthController::class . ':postDepartmentsByHospitalAjax')->setName('departments_by_name.ajax');
})->add(new GuestMiddleware($container));

$app->group('', function() use ($app) {
    $app->get('/home', MainController::class . ':dashboard')
        ->setName('home.dashboard');
    $app->get('/logout', UserAuthController::class . ':getLogout')
        ->setName('auth.logout');
    $app->get('/conversations', MessagesController::class . ':getConversations')
        ->setName('conversations.all');
    $app->get('/conversations/add/{recipient}', MessagesController::class . ':createConversation')
        ->setName('conversations.create');
    $app->get('/conversations/show/{id}', MessagesController::class . ':showConversation')
        ->setName('conversations.show');
    $app->post('/conversations/users_list', MessagesController::class . ':getUsersForNewConversations')
        ->setName('ajax.conversations.users_list');
    $app->post('/send_message', MessagesController::class . ':sendMessage')->setName('ajax.send_message')
        ->setName('ajax.send_message');

    $app->get('/histories', HistoriesController::class . ':getHistories')
        ->setName('histories.all');
    $app->get('/histories/add', HistoriesController::class . ':getAddHistory')
        ->setName('histories.add');
    $app->post('/histories/add', HistoriesController::class . ':postAddHistory')
        ->setName('histories.add.post');
    $app->get('/histories/delete/{id}', HistoriesController::class . ':postDeleteHistory')
        ->setName('histories.delete.post');
    $app->get('/histories/update/{id}', HistoriesController::class . ':getUpdateHistory')
        ->setName('histories.update');
    $app->post('/histories/update/{id}', HistoriesController::class . ':postUpdateHistory')
        ->setName('histories.update.post');
    $app->get('/histories/user/{id}', HistoriesController::class . ':getUserHistories')
        ->setName('histories.user_histories');
    $app->get('/histories/doctors', HistoriesController::class . ':getPermittedDoctors')
        ->setName('histories.permitted_doctors');
    
    $app->post('/histories/ajax/doctors/available_for_permission', HistoriesController::class . ':getDoctorsAvailableForPermission')
        ->setName('histories.doctorsAvailableForPermission');
    
    $app->get('/results', ResultsController::class . ':getResults')
        ->setName('results.all');
    $app->get('/results/add', ResultsController::class . ':getAddResult')
        ->setName('results.add');
    $app->post('/results', ResultsController::class . ':postResult')
        ->setName('results.add.post');
    $app->get('/results/delete/{id}', ResultsController::class . ':postDeleteResult')
        ->setName('results.delete.post');
    
    $app->get('/insurances', InsurancesController::class . ':getInsurances')
        ->setName('insurances.all');
    $app->get('/insurances/{type}', InsurancesController::class . ':showInsurance')
        ->setName('insurances.show');
    $app->get('/payment', InsurancesController::class . ':getPayment')
        ->setName('insurances.payment');
    
    $app->get('/appointments', AppointmentsController::class . ':getAppointments')
        ->setName('appointments.all');
    $app->get('/appointments/add', AppointmentsController::class . ':getAddAppointments')
        ->setName('appointments.add');
    $app->post('/appointments', AppointmentsController::class . ':postAppointment')
        ->setName('appointments.add.post');

    $app->get('/notifications/{id}', NotificationsController::class . ':getNotification')
        ->setName('notifications.show');
    
})->add(new AuthMiddleware($container));

$app->group('', function() use ($app) {
    $app->get('/about', MainController::class . ':getAbout')
        ->setName('about.show');
    $app->get('/contact', MainController::class . ':getContactUs')
        ->setName('contact.show');
    $app->get('/updates', UpdatesController::class . ':getAllUpdates')
        ->setName('updates.all');
    $app->get('/updates/{id}', UpdatesController::class . ':getUpdate')
        ->setName('updates.show');
    $app->post('/updates/add', UpdatesController::class . ':postUpdate')
        ->setName('updates.add.post');
    $app->get('/illnesses', IllnessesController::class . ':getAllIllnesses')
        ->setName('illnesses.all');
    $app->get('/illnesses/{id}', IllnessesController::class . ':getIllness')
        ->setName('illnesses.show');
    $app->get('/illnesses/letter/{letter}', IllnessesController::class . ':getIllnessesByLetter')
        ->setName('illnesses.by_letter');
    $app->post('/illnesses/add', IllnessesController::class . ':postIllness')
        ->setName('illnesses.add.post');
});

$app->group('/admin', function() use ($app) {
    $app->get('/', UsersAdminController::class . ':getLogin');
    $app->get('/login', UsersAdminController::class . ':getLogin')
        ->setName('admin.login');
    $app->post('/login', UsersAdminController::class . ':postLogin')
    ->setName('admin.login.post');
    
})->add(new AdminGuestMiddleware($container));

$app->group('/admin', function() use ($app) {   
    $app->get('/updates/all', UpdatesAdminController::class . ':getAllUpdates')
        ->setName('admin.updates.all');
    $app->get('/updates/add', UpdatesAdminController::class . ':getAddUpdates')
        ->setName('admin.updates.add');
    $app->post('/updates/add', UpdatesAdminController::class . ':postAddUpdates')
        ->setName('admin.updates.add.post');
    $app->get('/updates/delete/{id}', UpdatesAdminController::class . ':postDeleteUpdate')
        ->setName('admin.updates.delete.post');    
    $app->post('/updates/update/{id}', UpdatesAdminController::class . ':postUpdateUpdate')
        ->setName('admin.updates.update.post');
    $app->get('/updates/update/{id}', UpdatesAdminController::class . ':getUpdateUpdate')
        ->setName('admin.updates.update');
    
    
    $app->get('/appointments/all', AppointmentsAdminController::class . ':getAllAppointments')
        ->setName('admin.appointments.all');
    $app->get('/appointments/delete/{id}', AppointmentsAdminController::class . ':postDeleteAppointment')
        ->setName('admin.appointments.delete.post');
    
    $app->get('/histories/all', HistoriesAdminController::class . ':getAllHistories')
        ->setName('admin.histories.all');
    $app->get('/histories/add', HistoriesAdminController::class . ':getAddHistories')
        ->setName('admin.histories.add');
    $app->post('/histories/add', HistoriesAdminController::class . ':postAddHistories')
        ->setName('admin.histories.add.post');
    $app->get('/histories/delete/{id}', HistoriesAdminController::class . ':postDeleteHistory')
        ->setName('admin.histories.delete.post');
    
    
    $app->get('/illnesses/all', IllnessesAdminController::class . ':getAllIllnesses')
        ->setName('admin.illnesses.all');
    $app->get('/illnesses/add', IllnessesAdminController::class . ':getAddIllnesses')
        ->setName('admin.illnesses.add');
    $app->post('/illnesses/add', IllnessesAdminController::class . ':postAddIllnesses')
        ->setName('admin.illnesses.add.post');
    $app->get('/illnesses/delete/{id}', IllnessesAdminController::class . ':postDeleteIllness')
        ->setName('admin.illnesses.delete.post');
    $app->post('/illnesses/update/{id}', IllnessesAdminController::class . ':postUpdateIllnesses')
        ->setName('admin.illnesses.update.post');
    $app->get('/illnesses/update/{id}', IllnessesAdminController::class . ':getUpdateIllnesses')
        ->setName('admin.illnesses.update');
    
    
    $app->get('/insurances/all', InsurancesAdminController::class . ':getAllInsurances')
        ->setName('admin.insurances.all');
    $app->get('/insurances/add', InsurancesAdminController::class . ':getAddInsurances')
        ->setName('admin.insurances.add');
    $app->post('/insurances/add', InsurancesAdminController::class . ':postAddInsurances')
        ->setName('admin.insurances.add.post');
    $app->get('/insurances/delete/{id}', InsurancesAdminController::class . ':postDeleteInsurance')
        ->setName('admin.insurances.delete.post');
    $app->get('/insurances/update/{id}', InsurancesAdminController::class . ':getUpdateInsurance')
        ->setName('admin.insurances.update');
    $app->post('/insurances/update/{id}', InsurancesAdminController::class . ':postUpdateInsurance')
        ->setName('admin.insurances.update.post');
    
    
    $app->get('/users/all', UsersAdminController::class . ':getAllUsers')
        ->setName('admin.users.all');
    $app->get('/users/roles', UsersAdminController::class . ':getManageRoles')
        ->setName('admin.users.roles');
    $app->get('/users/roles/update/{id}', UsersAdminController::class . ':getRoleUpdateUser')
        ->setName('admin.users.role.update');
    $app->get('/users/delete/{id}', UsersAdminController::class . ':postDeleteUser')
        ->setName('admin.users.delete.post');
    $app->get('/users/update/{id}', UsersAdminController::class . ':postUpdateUser')
        ->setName('admin.users.update');
    $app->post('/users/update/{id}', UsersAdminController::class . ':postUpdateUser')
        ->setName('admin.users.update.post');

    
    $app->get('/admins/all', UsersAdminController::class . ':getAllAdmins')
        ->setName('admin.all');
    $app->get('/admins/add', UsersAdminController::class . ':getAddAdmin')
        ->setName('admin.add');
        
    $app->get('/results/all', ResultsAdminController::class . ':getAllResults')
        ->setName('admin.results.all');
    $app->get('/results/delete/{id}', ResultsAdminController::class . ':postDeleteResult')
        ->setName('admin.results.delete.post');
    $app->get('/logout', UsersAdminController::class . ':getLogout')
        ->setName('admin.logout');
    
})->add(new AdminAuthMiddleware($container)); 
