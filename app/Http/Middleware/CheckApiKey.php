<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mas Dayat harus mengirimkan header 'X-API-KEY' saat melakukan request
        $apiKey = $request->header('X-API-KEY');
        
        // Ambil kunci rahasia dari file .env
        $validKey = env('API_KEY_DIKSERA'); 

        // Jika tidak ada token atau token salah, tolak aksesnya
        if (!$apiKey || $apiKey !== $validKey) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. API Key tidak valid atau tidak ditemukan.'
            ], 401);
        }

        return $next($request);
    }
}