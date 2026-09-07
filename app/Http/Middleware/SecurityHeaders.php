<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Enforce explicit enterprise security headers that guarantee Livewire & AlpineJS script execution.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // CSP explicite autorisant Livewire, AlpineJS, styles et médias
        $response->headers->set('Content-Security-Policy', "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; font-src 'self' data: https:; img-src 'self' data: https: blob:; connect-src 'self' https: wss:;");
        
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
