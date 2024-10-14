<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => [
                'https://drive.google.com',
                'https://almacenesbomba.com',
                'http://localhost:8000',
                'http://localhost:8005',
                'http://localhost:8095',
                'http://central.almapa.info',
                'https://central.almapa.info'
            ],
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Access-Control-Allow-Headers' => '*',
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->headers($headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}