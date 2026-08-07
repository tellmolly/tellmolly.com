<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * TLS terminates at the Cloudflare tunnel, so requests arrive here as plain
     * HTTP. Left unset, this middleware trusts nothing, X-Forwarded-Proto is
     * ignored and every generated URL comes out http:// on an https:// page --
     * which the browser then blocks as mixed content. Trusting any proxy is
     * safe here: the container is reachable only through the tunnel and the
     * internal Docker network.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
