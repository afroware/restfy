<?php

namespace Afroware\Restfy\Tests\Stubs;

use Afroware\Restfy\Http\Request;
use Illuminate\Support\Collection;
use Afroware\Restfy\Transformer\Binding;
use Afroware\Restfy\Contract\Transformer\Adapter;

class TransformerStub implements Adapter
{
    public function transform($response, $transformer, Binding $binding, Request $request)
    {
        if ($response instanceof Collection) {
            return $response->transform(function ($response) use ($transformer) {
                return $transformer->transform($response);
            })->toArray();
        }

        return $transformer->transform($response);
    }
}
