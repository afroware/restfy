<?php

namespace Afroware\Restfy\Auth\Provider;

use Exception;
use Afroware\JwTauth\JwTauth;
use Illuminate\Http\Request;
use Afroware\Restfy\Routing\Route;
use Afroware\JwTauth\Exceptions\JwTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwT extends Authorization
{
    /**
     * The JwTauth instance.
     *
     * @var \Afroware\JwTauth\JwTauth
     */
    protected $auth;

    /**
     * Create a new JwT provider instance.
     *
     * @param \Afroware\JwTauth\JwTauth $auth
     *
     * @return void
     */
    public function __construct(JwTauth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Authenticate request with a JwT.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Afroware\Restfy\Routing\Route $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route)
    {
        $token = $this->getToken($request);

        try {
            if (! $user = $this->auth->setToken($token)->authenticate()) {
                throw new UnauthorizedHttpException('JwTauth', 'Unable to authenticate with invalid token.');
            }
        } catch (JwTException $exception) {
            throw new UnauthorizedHttpException('JwTauth', $exception->getMessage(), $exception);
        }

        return $user;
    }

    /**
     * Get the JwT from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getToken(Request $request)
    {
        try {
            $this->validateAuthorizationHeader($request);

            $token = $this->parseAuthorizationHeader($request);
        } catch (Exception $exception) {
            if (! $token = $request->query('token', false)) {
                throw $exception;
            }
        }

        return $token;
    }

    /**
     * Parse JwT from the authorization header.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function parseAuthorizationHeader(Request $request)
    {
        return trim(str_ireplace($this->getAuthorizationMethod(), '', $request->header('authorization')));
    }

    /**
     * Get the providers authorization method.
     *
     * @return string
     */
    public function getAuthorizationMethod()
    {
        return 'bearer';
    }
}
