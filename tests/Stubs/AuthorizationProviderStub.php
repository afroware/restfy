<?php

namespace Afroware\Restfy\Tests\Stubs;

use Illuminate\Http\Request;
use Afroware\Restfy\Routing\Route;
use Afroware\Restfy\Auth\Provider\Authorization;

class AuthorizationProviderStub extends Authorization
{
    public function authenticate(Request $request, Route $route)
    {
        $this->validateAuthorizationHeader($request);
    }

    public function getAuthorizationMethod()
    {
        return 'foo';
    }
}
