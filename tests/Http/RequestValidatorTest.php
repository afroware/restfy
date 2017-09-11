<?php

namespace Afroware\Restfy\Tests\Http;

use Illuminate\Http\Request;
use PHPUnit_Framework_TestCase;
use Illuminate\Container\Container;
use Afroware\Restfy\Http\RequestValidator;
use Afroware\Restfy\Tests\Stubs\HttpValidatorStub;
use Afroware\Restfy\Http\Parser\Accept as AcceptParser;

class RequestValidatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = new Container;
        $this->container->instance(AcceptParser::class, new AcceptParser('vnd', 'test', 'v1', 'json'));
        $this->validator = new RequestValidator($this->container);
    }

    public function testValidationFailsWithNoValidators()
    {
        $this->validator->replace([]);

        $this->assertFalse($this->validator->validateRequest(Request::create('foo', 'GET')), 'Validation passed when there were no validators.');
    }

    public function testValidationFails()
    {
        $this->validator->replace([HttpValidatorStub::class]);

        $this->assertFalse($this->validator->validateRequest(Request::create('foo', 'GET')), 'Validation passed when given a GET request.');
    }

    public function testValidationPasses()
    {
        $this->validator->replace([HttpValidatorStub::class]);

        $this->assertTrue($this->validator->validateRequest(Request::create('foo', 'POST')), 'Validation failed when given a POST request.');
    }
}
