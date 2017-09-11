<?php

namespace Afroware\Restfy\Tests\Transformer;

use Mockery;
use PHPUnit_Framework_TestCase;
use Afroware\Restfy\Transformer\Factory;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Afroware\Restfy\Tests\Stubs\UserStub;
use Afroware\Restfy\Tests\Stubs\TransformerStub;
use Afroware\Restfy\Tests\Stubs\UserTransformerStub;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $container = new Container;
        $container['request'] = Mockery::mock('Afroware\Restfy\Http\Request');

        $this->factory = new Factory($container, new TransformerStub);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testResponseIsTransformable()
    {
        $this->assertFalse($this->factory->transformableResponse(new UserStub('Jason'), new UserTransformerStub));

        $this->factory->register('Afroware\Restfy\Tests\Stubs\UserStub', new UserTransformerStub);

        $this->assertTrue($this->factory->transformableResponse(new UserStub('Jason'), new UserTransformerStub));
    }

    public function testRegisterParameterOrder()
    {
        // Third parameter is parameters and fourth is callback.
        $binding = $this->factory->register('Afroware\Restfy\Tests\Stubs\UserStub', new UserTransformerStub, ['foo' => 'bar'], function ($foo) {
            $this->assertSame('foo', $foo);
        });

        $binding->fireCallback('foo');
        $this->assertSame(['foo' => 'bar'], $binding->getParameters());

        // Third parameter is parameters and fourth is null.
        $binding = $this->factory->register('Afroware\Restfy\Tests\Stubs\UserStub', new UserTransformerStub, ['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $binding->getParameters());

        // Third parameter is callback and fourth is null.
        $binding = $this->factory->register('Afroware\Restfy\Tests\Stubs\UserStub', new UserTransformerStub, function ($foo) {
            $this->assertSame('foo', $foo);
        });

        $binding->fireCallback('foo');
    }

    public function testResponseIsTransformableType()
    {
        $this->assertFalse($this->factory->transformableType(['foo' => 'bar']));
        $this->assertTrue($this->factory->transformableType('Foo'));
        $this->assertTrue($this->factory->transformableType((object) ['foo' => 'bar']));
    }

    public function testTransformingResponse()
    {
        $this->factory->register('Afroware\Restfy\Tests\Stubs\UserStub', new UserTransformerStub);

        $response = $this->factory->transform(new UserStub('Jason'));

        $this->assertSame(['name' => 'Jason'], $response);
    }

    public function testTransformingCollectionResponse()
    {
        $this->factory->register('Afroware\Restfy\Tests\Stubs\UserStub', new UserTransformerStub);

        $response = $this->factory->transform(new Collection([new UserStub('Jason'), new UserStub('Bob')]));

        $this->assertSame([['name' => 'Jason'], ['name' => 'Bob']], $response);
    }

    public function testTransforingWithIlluminateRequest()
    {
        $container = new Container;
        $container['request'] = new \Illuminate\Http\Request();

        $factory = new Factory($container, new TransformerStub);

        $factory->register('Afroware\Restfy\Tests\Stubs\UserStub', new UserTransformerStub);

        $response = $factory->transform(new UserStub('Jason'));

        $this->assertSame(['name' => 'Jason'], $response);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to find bound transformer for "Afroware\Restfy\Tests\Stubs\UserStub" class
     */
    public function testTransformingWithNoTransformerThrowsException()
    {
        $this->factory->transform(new UserStub('Jason'));
    }
}
