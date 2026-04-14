<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stops shared proxies (LiteSpeed, CDNs, browsers) from caching Laravel HTML that embeds
 *
 * @vite URLs. Without this, an old page can keep pointing at old hashed CSS/JS after deploy.
 */
class PreventStaleHtmlCache
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $this->shouldApply($request, $response)) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    protected function shouldApply(Request $request, Response $response): bool
    {
        if (! in_array($response->getStatusCode(), [200, 302, 303, 307, 308], true)) {
            return false;
        }

        $type = (string) $response->headers->get('Content-Type', '');

        return str_contains($type, 'text/html');
    }
}
