<?php


if (! function_exists('version')) {
    /**
     * Set the version to generate API URLs to.
     *
     * @param string $version
     *
     * @return \Afroware\Restfy\Routing\UrlGenerator
     */
    function version($version)
    {
        return app('restfy.url')->version($version);
    }
}
