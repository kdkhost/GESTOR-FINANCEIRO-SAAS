<?php

namespace App\Modules\Pwa\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PwaController extends Controller
{
    /**
     * Retorna o manifest.json dinamico do PWA.
     */
    public function manifest(): Response
    {
        $manifest = [
            'name'             => configuracao('sistema_nome', config('app.name')),
            'short_name'       => configuracao('pwa_short_name', 'Financeiro'),
            'description'      => configuracao('sistema_descricao', 'Sistema de gestao financeira'),
            'start_url'        => '/',
            'display'          => 'standalone',
            'background_color' => configuracao('pwa_background_color', '#ffffff'),
            'theme_color'      => configuracao('pwa_theme_color', '#2563eb'),
            'orientation'      => 'portrait-primary',
            'icons'            => [
                [
                    'src'   => asset('images/pwa/icon-192.png'),
                    'sizes' => '192x192',
                    'type'  => 'image/png',
                ],
                [
                    'src'   => asset('images/pwa/icon-512.png'),
                    'sizes' => '512x512',
                    'type'  => 'image/png',
                ],
            ],
        ];

        return response(json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 200)
            ->header('Content-Type', 'application/manifest+json');
    }

    /**
     * Retorna o service worker.
     */
    public function serviceWorker(): Response
    {
        $sw = "
const CACHE_NAME = 'financeiro-saas-v1';
const urlsToCache = ['/'];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => response || fetch(event.request))
    );
});
";
        return response($sw, 200)->header('Content-Type', 'application/javascript');
    }
}