<?php

namespace Afroware\Restfy\Tests\Stubs;

use Closure;

class MiddlewareStub
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
