<?php

namespace Afroware\Restfy\Tests\Stubs;

use Illuminate\Routing\Controller;

class RoutingControllerOtherStub extends Controller
{
    public function find()
    {
        return 'baz';
    }
}
