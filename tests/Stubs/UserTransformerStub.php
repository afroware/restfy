<?php

namespace Afroware\Restfy\Tests\Stubs;

class UserTransformerStub
{
    public function transform(UserStub $user)
    {
        return [
            'name' => $user->name,
        ];
    }
}
