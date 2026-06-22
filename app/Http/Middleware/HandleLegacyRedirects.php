<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleLegacyRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        if ($request->is('admin*') || $request->is('livewire-*')) {
            return $next($request);
        }

        $path = Redirect::normalizePath($request->path());

        $redirect = Redirect::query()
            ->active()
            ->where('source_path', $path)
            ->first();

        if (! $redirect) {
            return $next($request);
        }

        $target = $redirect->destination_url;

        if (Redirect::normalizePath(parse_url($target, PHP_URL_PATH) ?: '/') === $path) {
            return $next($request);
        }

        $redirect->forceFill([
            'match_hits' => $redirect->match_hits + 1,
            'last_matched_at' => now(),
        ])->save();

        return redirect()->to($target, $redirect->status_code);
    }
}
