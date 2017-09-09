<?php

namespace Afroware\Restfy\Tests\Stubs;

use Illuminate\Container\Container;
use Afroware\Restfy\Http\RateLimit\Throttle\Throttle;

class ThrottleStub extends Throttle
{
    protected $enabled;

    public function __construct(array $options = ['limit' => 60, 'expires' => 60], $enabled = true)
    {
        $this->enabled = $enabled;

        parent::__construct($options);
    }

    public function match(Container $app)
    {
        return $this->enabled;
    }
}
