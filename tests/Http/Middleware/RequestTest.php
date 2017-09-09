<?php

namespace Afroware\Restfy\Tests\Http\Middleware;

use Mockery as m;
use Afroware\Restfy\Http\Request;
use Afroware\Restfy\Routing\Router;
use Afroware\Restfy\Http\Validation;
use PHPUnit_Framework_TestCase;
use Afroware\Restfy\Exception\Handler;
use Afroware\Restfy\Http\Validation\Accept;
use Afroware\Restfy\Http\Validation\Domain;
use Afroware\Restfy\Http\Validation\Prefix;
use Afroware\Restfy\Http\RequestValidator;
use Afroware\Restfy\Tests\Stubs\ApplicationStub;
use Afroware\Restfy\Http\Parser\Accept as AcceptParser;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Events\Dispatcher as EventDispatcher;
use Afroware\Restfy\Contract\Http\Request as RequestContract;
use Afroware\Restfy\Http\Middleware\Request as RequestMiddleware;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->app = new ApplicationStub;
        $this->router = m::mock(Router::class);
        $this->validator = new RequestValidator($this->app);
        $this->handler = m::mock(Handler::class);
        $this->events = new EventDispatcher($this->app);

        $this->app->alias(Request::class, RequestContract::class);

        $this->middleware = new RequestMiddleware($this->app, $this->handler, $this->router, $this->validator, $this->events);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testNoPrefixOrDomainDoesNotMatch()
    {
        $this->app[Domain::class] = new Validation\Domain(null);
        $this->app[Prefix::class] = new Validation\Prefix(null);
        $this->app[Accept::class] = new Validation\Accept(new AcceptParser('vnd', 'restfy', 'v1', 'json'));

        $request = Request::create('foo', 'GET');

        $this->middleware->handle($request, function ($handled) use ($request) {
            $this->assertSame($handled, $request);
        });
    }

    public function testPrefixMatchesAndSendsRequestThroughRouter()
    {
        $this->app[Domain::class] = new Validation\Domain(null);
        $this->app[Prefix::class] = new Validation\Prefix('/');
        $this->app[Accept::class] = new Validation\Accept(new AcceptParser('vnd', 'restfy', 'v1', 'json'));

        $request = IlluminateRequest::create('foo', 'GET');

        $this->router->shouldReceive('dispatch')->once();

        $this->middleware->handle($request, function () {
            //
        });

        $this->app[Domain::class] = new Validation\Domain(null);
        $this->app[Prefix::class] = new Validation\Prefix('bar');
        $this->app[Accept::class] = new Validation\Accept(new AcceptParser('vnd', 'restfy', 'v1', 'json'));

        $request = IlluminateRequest::create('bar/foo', 'GET');

        $this->router->shouldReceive('dispatch')->once();

        $this->middleware->handle($request, function () {
            //
        });

        $request = IlluminateRequest::create('bing/bar/foo', 'GET');

        $this->middleware->handle($request, function ($handled) use ($request) {
            $this->assertSame($handled, $request);
        });
    }

    public function testDomainMatchesAndSendsRequestThroughRouter()
    {
        $this->app[Domain::class] = new Validation\Domain('foo.bar');
        $this->app[Prefix::class] = new Validation\Prefix(null);
        $this->app[Accept::class] = new Validation\Accept(new AcceptParser('vnd', 'restfy', 'v1', 'json'));

        $request = IlluminateRequest::create('http://foo.bar/baz', 'GET');

        $this->router->shouldReceive('dispatch')->once();

        $this->middleware->handle($request, function () {
            //
        });

        $request = IlluminateRequest::create('http://bing.foo.bar/baz', 'GET');

        $this->middleware->handle($request, function ($handled) use ($request) {
            $this->assertSame($handled, $request);
        });
    }
}
