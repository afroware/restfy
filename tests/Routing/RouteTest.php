<?php

namespace Afroware\Restfy\Tests\Routing;

use Mockery as m;
use Afroware\Restfy\Http\Request;
use Afroware\Restfy\Routing\Route;
use PHPUnit_Framework_TestCase;
use Illuminate\Container\Container;
use Afroware\Restfy\Tests\Stubs\RoutingAdapterStub;
use Illuminate\Routing\Route as IlluminateRoute;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->adapter = new RoutingAdapterStub;
        $this->container = new Container;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testCreatingNewRoute()
    {
        $request = Request::create('foo', 'GET');

        $route = new Route($this->adapter, $this->container, $request, new IlluminateRoute(['GET', 'HEAD'], 'foo', [
            'scopes' => ['foo', 'bar'],
            'providers' => ['foo'],
            'limit' => 5,
            'expires' => 10,
            'throttle' => 'Afroware\Restfy\Tests\Stubs\BasicThrottleStub',
            'version' => ['v1'],
            'conditionalRequest' => false,
            'middleware' => 'foo.bar',
        ]));

        $this->assertSame(['foo', 'bar'], $route->scopes(), 'Route did not setup scopes correctly.');
        $this->assertSame(['foo'], $route->getAuthenticationProviders(), 'Route did not setup authentication providers correctly.');
        $this->assertSame(5, $route->getRateLimit(), 'Route did not setup rate limit correctly.');
        $this->assertSame(10, $route->getRateLimitExpiration(), 'Route did not setup rate limit expiration correctly.');
        $this->assertTrue($route->hasThrottle(), 'Route did not setup throttle correctly.');
        $this->assertInstanceOf('Afroware\Restfy\Tests\Stubs\BasicThrottleStub', $route->getThrottle(), 'Route did not setup throttle correctly.');
        $this->assertFalse($route->requestIsConditional(), 'Route did not setup conditional request correctly.');
    }

    public function testControllerOptionsMergeAndOverrideRouteOptions()
    {
        $request = Request::create('foo', 'GET');

        $route = new Route($this->adapter, $this->container, $request, new IlluminateRoute(['GET', 'HEAD'], 'foo', [
            'scopes' => ['foo', 'bar'],
            'providers' => ['foo'],
            'limit' => 5,
            'expires' => 10,
            'throttle' => 'Afroware\Restfy\Tests\Stubs\ThrottleStub',
            'version' => ['v1'],
            'conditionalRequest' => false,
            'uses' => 'Afroware\Restfy\Tests\Stubs\RoutingControllerStub@index',
            'middleware' => 'foo.bar',
        ]));

        $this->assertSame(['foo', 'bar', 'baz', 'bing'], $route->scopes(), 'Route did not setup scopes correctly.');
        $this->assertSame(['foo', 'red', 'black'], $route->getAuthenticationProviders(), 'Route did not setup authentication providers correctly.');
        $this->assertSame(10, $route->getRateLimit(), 'Route did not setup rate limit correctly.');
        $this->assertSame(20, $route->getRateLimitExpiration(), 'Route did not setup rate limit expiration correctly.');
        $this->assertTrue($route->hasThrottle(), 'Route did not setup throttle correctly.');
        $this->assertInstanceOf('Afroware\Restfy\Tests\Stubs\BasicThrottleStub', $route->getThrottle(), 'Route did not setup throttle correctly.');

        $route = new Route($this->adapter, $this->container, $request, new IlluminateRoute(['GET', 'HEAD'], 'foo/bar', [
            'scopes' => ['foo', 'bar'],
            'providers' => ['foo'],
            'limit' => 5,
            'expires' => 10,
            'throttle' => 'Afroware\Restfy\Tests\Stubs\ThrottleStub',
            'version' => ['v1'],
            'conditionalRequest' => false,
            'uses' => 'Afroware\Restfy\Tests\Stubs\RoutingControllerStub@show',
        ]));

        $this->assertSame(['foo', 'bar', 'baz', 'bing', 'bob'], $route->scopes(), 'Route did not setup scopes correctly.');
        $this->assertSame(['foo'], $route->getAuthenticationProviders(), 'Route did not setup authentication providers correctly.');
        $this->assertSame(10, $route->getRateLimit(), 'Route did not setup rate limit correctly.');
        $this->assertSame(20, $route->getRateLimitExpiration(), 'Route did not setup rate limit expiration correctly.');
        $this->assertTrue($route->hasThrottle(), 'Route did not setup throttle correctly.');
        $this->assertInstanceOf('Afroware\Restfy\Tests\Stubs\BasicThrottleStub', $route->getThrottle(), 'Route did not setup throttle correctly.');
    }
}
