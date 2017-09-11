<?php

namespace Afroware\Restfy\Contract\Http\RateLimit;

use Afroware\Restfy\Http\Request;
use Illuminate\Container\Container;

interface HasRateLimiter
{
    /**
     * Get rate limiter callable.
     *
     * @param \Illuminate\Container\Container $app
     * @param \Afroware\Restfy\Http\Request         $request
     *
     * @return string
     */
    public function getRateLimiter(Container $app, Request $request);
}
