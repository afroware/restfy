<?php

namespace Afroware\Restfy\Tests\Stubs;

class UserStub
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
