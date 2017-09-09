<?php

namespace Afroware\Restfy\Tests\Stubs;

use Illuminate\Http\Request;
use Afroware\Restfy\Contract\Http\Validator;

class HttpValidatorStub implements Validator
{
    public function validate(Request $request)
    {
        return $request->getMethod() === 'POST';
    }
}
