<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Batasi route hanya untuk role tertentu. Contoh: ->middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }

            return redirect()->route('dashboard')->with('swal', [
                'icon'  => 'error',
                'title' => 'Akses Ditolak',
                'text'  => 'Anda tidak memiliki akses ke halaman tersebut.',
            ]);
        }

        return $next($request);
    }
}
