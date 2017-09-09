<?php

namespace Afroware\Restfy\Contract\Http;

use Illuminate\Http\Request as IlluminateRequest;

interface Request
{
    /**
     * Create a new Afroware request instance from an Illuminate request instance.
     *
     * @param \Illuminate\Http\Request $old
     *
     * @return \Afroware\Restfy\Http\Request
     */
    public function createFromIlluminate(IlluminateRequest $old);
}
