<?php

namespace Afroware\Restfy\Tests\Stubs;

use Illuminate\Container\Container;
use Afroware\Restfy\Contract\Http\RateLimit\Throttle;

class BasicThrottleStub implements Throttle
{
    public function match(Container $app)
    {
        return true;
    }

    public function getLimit()
    {
        return 15;
    }

    public function getExpires()
    {
        return 10;
    }
}
