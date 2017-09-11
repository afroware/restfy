<?php

namespace Afroware\Restfy\Facade;

use Afroware\Restfy\Http\InternalRequest;
use Illuminate\Support\Facades\Facade;

class API extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'restfy.dispatcher';
    }

    /**
     * Bind an exception handler.
     *
     * @param callable $callback
     *
     * @return void
     */
    public static function error(callable $callback)
    {
        return static::$app['restfy.exception']->register($callback);
    }

    /**
     * Register a class transformer.
     *
     * @param string          $class
     * @param string|\Closure $transformer
     *
     * @return \Afroware\Restfy\Transformer\Binding
     */
    public static function transform($class, $transformer)
    {
        return static::$app['restfy.transformer']->register($class, $transformer);
    }

    /**
     * Get the authenticator.
     *
     * @return \Afroware\Restfy\Auth\Auth
     */
    public static function auth()
    {
        return static::$app['restfy.auth'];
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Auth\GenericUser|\Illuminate\Database\Eloquent\Model
     */
    public static function user()
    {
        return static::$app['restfy.auth']->user();
    }

    /**
     * Determine if a request is internal.
     *
     * @return bool
     */
    public static function internal()
    {
        return static::$app['restfy.router']->getCurrentRequest() instanceof InternalRequest;
    }

    /**
     * Get the response factory to begin building a response.
     *
     * @return \Afroware\Restfy\Http\Response\Factory
     */
    public static function response()
    {
        return static::$app['restfy.http.response'];
    }

    /**
     * Get the API router instance.
     *
     * @return \Afroware\Restfy\Routing\Router
     */
    public static function router()
    {
        return static::$app['restfy.router'];
    }
}
