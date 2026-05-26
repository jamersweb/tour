<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class LegacyMediaController extends Controller
{
    public function show(string $path): Response
    {
        abort_if(str_contains($path, '..'), 404);

        $upstream = Http::timeout(15)
            ->retry(1, 200)
            ->get('https://acutetourism.org/uploads/'.ltrim($path, '/'));

        abort_unless($upstream->successful(), 404);

        $contentType = $upstream->header('Content-Type', 'application/octet-stream');

        return response($upstream->body(), 200)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=604800');
    }
}
