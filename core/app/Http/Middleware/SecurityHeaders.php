<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $scriptSources = [
            "'self'",
            "'unsafe-inline'",
            "'unsafe-eval'",
            'https:',
        ];

        $connectSources = [
            "'self'",
            'https:',
            'wss:',
        ];

        if (app()->environment('local')) {
            $scriptSources = [
                ...$scriptSources,
                'http://localhost:5173',
                'http://127.0.0.1:5173',
            ];

            $connectSources = [
                ...$connectSources,
                'http://localhost:5173',
                'http://127.0.0.1:5173',
                'ws://localhost:5173',
                'ws://127.0.0.1:5173',
            ];
        }

        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', [
                "default-src 'self'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
                "object-src 'none'",

                'script-src ' . implode(' ', $scriptSources),

                "style-src 'self' 'unsafe-inline' https:",

                "font-src 'self' data: https:",

                "img-src 'self' data: blob: https:",

                'connect-src ' . implode(' ', $connectSources),

                "frame-src 'self' https:",
            ])
        );

        $response->headers->set(
            'X-Frame-Options',
            'SAMEORIGIN'
        );

        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        if ($request->isSecure() && !app()->environment(['local', 'testing'])) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000'
            );
        }

        return $response;
    }
}