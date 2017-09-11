<?php

namespace Afroware\Restfy\Contract\Transformer;

use Afroware\Restfy\Http\Request;
use Afroware\Restfy\Transformer\Binding;

interface Adapter
{
    /**
     * Transform a response with a transformer.
     *
     * @param mixed                          $response
     * @param object                         $transformer
     * @param \Afroware\Restfy\Transformer\Binding $binding
     * @param \Afroware\Restfy\Http\Request        $request
     *
     * @return array
     */
    public function transform($response, $transformer, Binding $binding, Request $request);
}
