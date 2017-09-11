<?php

namespace Afroware\Restfy\Http\Middleware;

use Closure;
use Afroware\Restfy\Routing\Router;
use Afroware\Restfy\Auth\Auth as Authentication;

class Auth
{
    /**
     * Router instance.
     *
     * @var \Afroware\Restfy\Routing\Router
     */
    protected $router;

    /**
     * Authenticator instance.
     *
     * @var \Afroware\Restfy\Auth\Auth
     */
    protected $auth;

    /**
     * Create a new auth middleware instance.
     *
     * @param \Afroware\Restfy\Routing\Router $router
     * @param \Afroware\Restfy\Auth\Auth      $auth
     *
     * @return void
     */
    public function __construct(Router $router, Authentication $auth)
    {
        $this->router = $router;
        $this->auth = $auth;
    }

    /**
     * Perform authentication before a request is executed.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $this->router->getCurrentRoute();

        if (! $this->auth->check(false)) {
            $this->auth->authenticate($route->getAuthenticationProviders());
        }

        return $next($request);
    }
}
