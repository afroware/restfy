<?php

namespace Afroware\Restfy\Tests\Http\Validation;

use Illuminate\Http\Request;
use PHPUnit_Framework_TestCase;
use Afroware\Restfy\Http\Parser\Accept as AcceptParser;
use Afroware\Restfy\Http\Validation\Accept as AcceptValidator;

class AcceptTest extends PHPUnit_Framework_TestCase
{
    public function testValidationPassesForStrictModeAndOptionsRequests()
    {
        $parser = new AcceptParser('vnd', 'restfy', 'v1', 'json');
        $validator = new AcceptValidator($parser, true);

        $this->assertTrue($validator->validate(Request::create('bar', 'OPTIONS')), 'Validation failed when it should have passed with an options request.');
    }
}
