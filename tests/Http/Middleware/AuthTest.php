<?php

namespace Afroware\Restfy\Tests\Http\Middleware;

use Mockery as m;
use Afroware\Restfy\Auth\Auth;
use Afroware\Restfy\Http\Request;
use Afroware\Restfy\Routing\Route;
use Afroware\Restfy\Routing\Router;
use PHPUnit_Framework_TestCase;
use Illuminate\Container\Container;
use Afroware\Restfy\Tests\Stubs\RoutingAdapterStub;
use Illuminate\Routing\Route as IlluminateRoute;
use Afroware\Restfy\Http\Middleware\Auth as AuthMiddleware;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = new Container;
        $this->adapter = new RoutingAdapterStub;
        $this->router = m::mock(Router::class);
        $this->auth = m::mock(Auth::class);
        $this->middleware = new AuthMiddleware($this->router, $this->auth);
    }

    public function testProtectedRouteFiresAuthenticationAndPasses()
    {
        $request = Request::create('test', 'GET');

        $route = new Route($this->adapter, $this->container, $request, new IlluminateRoute('GET', '/test', [
            'providers' => [],
        ]));

        $this->auth->shouldReceive('check')->once()->with(false)->andReturn(false);
        $this->auth->shouldReceive('authenticate')->once()->with([])->andReturn(null);

        $this->router->shouldReceive('getCurrentRoute')->once()->andReturn($route);

        $this->middleware->handle($request, function ($handledRequest) use ($request) {
            $this->assertSame($handledRequest, $request);
        });
    }

    public function testProtectedRouteAlreadyLoggedIn()
    {
        $request = Request::create('test', 'GET');

        $route = new Route($this->adapter, $this->container, $request, new IlluminateRoute('GET', '/test', [
            'providers' => [],
        ]));

        $this->auth->shouldReceive('check')->once()->with(false)->andReturn(true);

        $this->router->shouldReceive('getCurrentRoute')->once()->andReturn($route);

        $this->middleware->handle($request, function ($handledRequest) use ($request) {
            $this->assertSame($handledRequest, $request);
        });
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function testAuthenticationFailsAndExceptionIsThrown()
    {
        $exception = new UnauthorizedHttpException('test');

        $request = Request::create('test', 'GET');

        $route = new Route($this->adapter, $this->container, $request, new IlluminateRoute('GET', '/test', [
            'providers' => [],
        ]));

        $this->auth->shouldReceive('check')->once()->with(false)->andReturn(false);
        $this->auth->shouldReceive('authenticate')->once()->with([])->andThrow($exception);

        $this->router->shouldReceive('getCurrentRoute')->once()->andReturn($route);

        $this->middleware->handle($request, function () {
            //
        });
    }
}
