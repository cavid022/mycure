<?php

use Respect\Validation\Validator as validator;

require __DIR__ . '/../vendor/autoload.php';
session_start();

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$app = new Slim\App([
  'settings' => [
        'displayErrorDetails' => 'true',
        // 'determineRouteBeforeAppMiddleware' => true,

        'app' => [
         'name' => 'app'
         ],

        'views' => [
            'cache' => getenv('VIEW_CACHE_DISABLED') === 'true' ? false : __DIR__ . '/../storage/views'
        ],
         
        'database' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'mycure',
            'username' => 'mycure',
            'password' => 'mycure',
            'charset' => 'utf8',
            'collocation' => 'utf8_unicode_ci',
            'prefix' => ''
        ],
    ]
  ]); 
 
$container = $app->getContainer();

require_once __DIR__ . '/database.php';
$app->db = $capsule;

$container['auth'] = function ($container) {
    return new \App\Auth\Auth;
};

$container['admin_auth'] = function ($container) {
    return new \App\Admin_auth\Admin_auth;
};

$container['remember_me'] = function ($container) {
    return new \App\Admin_auth\Remember_me;
};

$container['search'] = function ($container) {
    return new \App\Auth\search;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../app/Views', [
        'cache' => $container->settings['views']['cache']
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new Bearlikelion\TwigRecaptcha\Extension([
    	'public' => '6LeO7CYUAAAAABPsFfw6SlFxTajDgx1pWIqTmOqn',
    	'private' => '6LeO7CYUAAAAANOzbJTeTkRdjEWRi6_5YoqNcKTD'
    ]));
    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    $view->getEnvironment()->addGlobal('admin_auth', [
        'check' => $container->admin_auth->check(),
        'admin' => $container->admin_auth->admin(),
    ]);
    $view->getEnvironment()->addGlobal('remember_me', [
        'checkEmail' => $container->remember_me->checkEmail(),
        'checkPassword' => $container->remember_me->checkPassword(),
        'checkRememberMe' => $container->remember_me->checkRememberMe(),
    ]);
    
    $view->getEnvironment()->addGlobal('uploads', 'uploads');
    $view->getEnvironment()->addGlobal('assets', 'assets');
    $view->getEnvironment()->addGlobal('flash', $container->flash);
    
    require_once __DIR__ . '/filters.php';
    return $view;
};

$container['validator'] = function ($container) {
  return new App\Validation\Validator;  
};

$container['uploads_directory'] = __DIR__ . '/../public/uploads';

$container['flash'] = function ($container) {
    return new Slim\Flash\Messages;
};

$container['mail'] = function ($container) {
    $transport = Swift_MailTransport::newInstance();
    $mailer = Swift_Mailer::newInstance($transport);
    return $mailer;
};

$app->add(App\Middleware\ValidationErrorsMiddleware::class);
$app->add(App\Middleware\OldInputMiddleware::class);

validator::with('App\\Validation\\Rules\\');

require_once __DIR__ . '/../routes/web.php';