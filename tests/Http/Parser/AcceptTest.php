<?php

namespace Afroware\Restfy\Tests\Http\Parser;

use Afroware\Restfy\Http\Request;
use PHPUnit_Framework_TestCase;
use Afroware\Restfy\Http\Parser\Accept;

class AcceptTest extends PHPUnit_Framework_TestCase
{
    public function testParsingInvalidAcceptReturnsDefaults()
    {
        $parser = new Accept('vnd', 'restfy', 'v1', 'json');

        $accept = $parser->parse($this->createRequest('foo', 'GET', ['accept' => 'application/vnd.foo.v2+xml']));

        $this->assertSame('restfy', $accept['subtype']);
        $this->assertSame('v1', $accept['version']);
        $this->assertSame('json', $accept['format']);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedMessage Accept header could not be properly parsed because of a strict matching process.
     */
    public function testStrictlyParsingInvalidAcceptHeaderThrowsException()
    {
        $parser = new Accept('vnd', 'restfy', 'v1', 'json');

        $accept = $parser->parse($this->createRequest('foo', 'GET', ['accept' => 'application/vnd.foo.v2+xml']), true);
    }

    public function testParsingValidAcceptReturnsHeaderValues()
    {
        $parser = new Accept('vnd', 'restfy', 'v1', 'json');

        $accept = $parser->parse($this->createRequest('foo', 'GET', ['accept' => 'application/vnd.restfy.v2+xml']));

        $this->assertSame('restfy', $accept['subtype']);
        $this->assertSame('v2', $accept['version']);
        $this->assertSame('xml', $accept['format']);
    }

    public function testRestfyVersionWithoutVSuffix()
    {
        $parser = new Accept('vnd', 'restfy', '1.0', 'json');

        $accept = $parser->parse($this->createRequest('foo', 'GET', ['accept' => 'application/vnd.restfy.1.0+xml']));

        $this->assertSame('restfy', $accept['subtype']);
        $this->assertSame('1.0', $accept['version']);
        $this->assertSame('xml', $accept['format']);
    }

    public function testRestfyVersionWithHyphen()
    {
        $parser = new Accept('vnd', 'restfy', '1.0-beta', 'json');

        $accept = $parser->parse($this->createRequest('foo', 'GET', ['accept' => 'application/vnd.restfy.1.0-beta+xml']));

        $this->assertSame('restfy', $accept['subtype']);
        $this->assertSame('1.0-beta', $accept['version']);
        $this->assertSame('xml', $accept['format']);
    }

    protected function createRequest($uri, $method, array $headers = [])
    {
        $request = Request::create($uri, $method);

        foreach ($headers as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }
}
