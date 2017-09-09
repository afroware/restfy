<?php

namespace Afroware\Restfy\Provider;

use RuntimeException;
use Afroware\Restfy\Auth\Auth;
use Afroware\Restfy\Dispatcher;
use Afroware\Restfy\Http\Request;
use Afroware\Restfy\Http\Response;
use Afroware\Restfy\Console\Command;
use Afroware\Restfy\Exception\Handler as ExceptionHandler;
use Afroware\Restfy\Transformer\Factory as TransformerFactory;

class AfrowareServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setResponseStaticInstances();

        Request::setAcceptParser($this->app['Afroware\Restfy\Http\Parser\Accept']);

        $this->app->rebinding('restfy.routes', function ($app, $routes) {
            $app['restfy.url']->setRouteCollections($routes);
        });
    }

    protected function setResponseStaticInstances()
    {
        Response::setFormatters($this->config('formats'));
        Response::setTransformer($this->app['restfy.transformer']);
        Response::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $this->registerClassAliases();

        $this->app->register(RoutingServiceProvider::class);

        $this->app->register(HttpServiceProvider::class);

        $this->registerExceptionHandler();

        $this->registerDispatcher();

        $this->registerAuth();

        $this->registerTransformer();

        $this->registerDocsCommand();

        if (class_exists('Illuminate\Foundation\Application', false)) {
            $this->commands([
                'Afroware\Restfy\Console\Command\Cache',
                'Afroware\Restfy\Console\Command\Routes',
            ]);
        }
    }

    /**
     * Register the configuration.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/restfy.php'), 'restfy');

        if (! $this->app->runningInConsole() && empty($this->config('prefix')) && empty($this->config('domain'))) {
            throw new RuntimeException('Unable to boot RestfyServiceProvider, configure an API domain or prefix.');
        }
    }

    /**
     * Register the class aliases.
     *
     * @return void
     */
    protected function registerClassAliases()
    {
        $aliases = [
            'Afroware\Restfy\Http\Request' => 'Afroware\Restfy\Contract\Http\Request',
            'restfy.dispatcher' => 'Afroware\Restfy\Dispatcher',
            'restfy.http.validator' => 'Afroware\Restfy\Http\RequestValidator',
            'restfy.http.response' => 'Afroware\Restfy\Http\Response\Factory',
            'restfy.router' => 'Afroware\Restfy\Routing\Router',
            'restfy.router.adapter' => 'Afroware\Restfy\Contract\Routing\Adapter',
            'restfy.auth' => 'Afroware\Restfy\Auth\Auth',
            'restfy.limiting' => 'Afroware\Restfy\Http\RateLimit\Handler',
            'restfy.transformer' => 'Afroware\Restfy\Transformer\Factory',
            'restfy.url' => 'Afroware\Restfy\Routing\UrlGenerator',
            'restfy.exception' => ['Afroware\Restfy\Exception\Handler', 'Afroware\Restfy\Contract\Debug\ExceptionHandler'],
        ];

        foreach ($aliases as $key => $aliases) {
            foreach ((array) $aliases as $alias) {
                $this->app->alias($key, $alias);
            }
        }
    }

    /**
     * Register the exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        $this->app->singleton('restfy.exception', function ($app) {
            return new ExceptionHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], $this->config('errorFormat'), $this->config('debug'));
        });
    }

    /**
     * Register the internal dispatcher.
     *
     * @return void
     */
    public function registerDispatcher()
    {
        $this->app->singleton('restfy.dispatcher', function ($app) {
            $dispatcher = new Dispatcher($app, $app['files'], $app['Afroware\Restfy\Routing\Router'], $app['Afroware\Restfy\Auth\Auth']);

            $dispatcher->setSubtype($this->config('subtype'));
            $dispatcher->setStandardsTree($this->config('standardsTree'));
            $dispatcher->setPrefix($this->config('prefix'));
            $dispatcher->setDefaultVersion($this->config('version'));
            $dispatcher->setDefaultDomain($this->config('domain'));
            $dispatcher->setDefaultFormat($this->config('defaultFormat'));

            return $dispatcher;
        });
    }

    /**
     * Register the auth.
     *
     * @return void
     */
    protected function registerAuth()
    {
        $this->app->singleton('restfy.auth', function ($app) {
            return new Auth($app['Afroware\Restfy\Routing\Router'], $app, $this->config('auth'));
        });
    }

    /**
     * Register the transformer factory.
     *
     * @return void
     */
    protected function registerTransformer()
    {
        $this->app->singleton('restfy.transformer', function ($app) {
            return new TransformerFactory($app, $this->config('transformer'));
        });
    }

    /**
     * Register the documentation command.
     *
     * @return void
     */
    protected function registerDocsCommand()
    {
        $this->app->singleton('Afroware\Restfy\Console\Command\Docs', function ($app) {
            return new Command\Docs(
                $app['Afroware\Restfy\Routing\Router'],
                $app['Afroware\Blueprint\Blueprint'],
                $app['Afroware\Blueprint\Writer'],
                $this->config('name'),
                $this->config('version')
            );
        });

        $this->commands(['Afroware\Restfy\Console\Command\Docs']);
    }
}
