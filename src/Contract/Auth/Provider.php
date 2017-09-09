<?php

namespace Afroware\Restfy\Contract\Auth;

use Illuminate\Http\Request;
use Afroware\Restfy\Routing\Route;

interface Provider
{
    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Afroware\Restfy\Routing\Route $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route);
}
