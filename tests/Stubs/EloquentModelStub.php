<?php

namespace Afroware\Restfy\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class EloquentModelStub extends Model
{
    protected $table = 'foo_bar';

    public function toArray()
    {
        return ['foo' => 'bar'];
    }
}
