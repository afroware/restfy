<?php

namespace Afroware\Restfy\Tests\Http\RateLimit\Throttle;

use Mockery;
use Afroware\Restfy\Auth\Auth;
use PHPUnit_Framework_TestCase;
use Illuminate\Container\Container;
use Afroware\Restfy\Http\RateLimit\Throttle\Unauthenticated;

class UnauthenticatedTest extends PHPUnit_Framework_TestCase
{
    public function testThrottleMatchesCorrectly()
    {
        $auth = Mockery::mock(Auth::class)->shouldReceive('check')->once()->andReturn(true)->getMock();
        $container = new Container;
        $container['restfy.auth'] = $auth;

        $this->assertFalse((new Unauthenticated)->match($container));
    }
}
