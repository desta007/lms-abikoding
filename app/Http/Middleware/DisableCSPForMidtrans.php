<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableCSPForMidtrans
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Remove any existing CSP headers that might interfere with Midtrans
        $response->headers->remove('Content-Security-Policy');
        $response->headers->remove('X-Content-Security-Policy');
        $response->headers->remove('X-WebKit-CSP');
        
        // Set a permissive CSP for Midtrans - includes all required domains
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
               "https://snap-assets.al-pc-id-b.cdn.gtflabs.io " .
               "https://snap-assets.al-pc-id-c.cdn.gtflabs.io " .
               "https://snap-assets.al-pc-id-a.cdn.gtflabs.io " .
               "https://app.sandbox.midtrans.com https://app.midtrans.com " .
               "https://api.sandbox.midtrans.com https://api.midtrans.com " .
               "https://pay.google.com https://js-agent.newrelic.com " .
               "https://bam.nr-data.net https://gwk.gopayapi.com " .
               "https://*.gopayapi.com https://*.midtrans.com " .
               "https://*.veritrans.co.id https://*.gtflabs.io " .
               "https://*.cloudfront.net https://*.mixpanel.com " .
               "https://*.google-analytics.com https://code.jquery.com; " .
               "frame-src 'self' 'unsafe-inline' " .
               "https://*.midtrans.com https://*.veritrans.co.id " .
               "https://*.gopayapi.com https://*.gtflabs.io " .
               "https://*.cloudfront.net " .
               "https://app.sandbox.midtrans.com https://app.midtrans.com; " .
               "connect-src 'self' " .
               "https://*.midtrans.com https://*.veritrans.co.id " .
               "https://*.gopayapi.com https://*.gtflabs.io " .
               "https://*.cloudfront.net " .
               "https://api.sandbox.midtrans.com https://api.midtrans.com " .
               "https://bam.nr-data.net https://*.mixpanel.com " .
               "https://*.google-analytics.com; " .
               "img-src 'self' data: https: blob:; " .
               "style-src 'self' 'unsafe-inline' " .
               "https://fonts.googleapis.com https://fonts.bunny.net " .
               "https://*.midtrans.com https://*.gtflabs.io " .
               "https://*.cloudfront.net; " .
               "font-src 'self' data: " .
               "https://fonts.googleapis.com https://fonts.bunny.net " .
               "https://*.cloudfront.net; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self' https://*.midtrans.com;";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        return $response;
    }
}
