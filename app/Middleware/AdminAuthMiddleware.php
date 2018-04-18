<?php

namespace App\Middleware;

class AdminAuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if (!$this->container->admin_auth->check()) {
            return $response->withRedirect($this->container->router->pathFor('admin.login'));
        }

        $response = $next($request, $response);
        return $response;
    }
}
