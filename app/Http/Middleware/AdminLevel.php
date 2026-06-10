<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminLevel
{
    public function handle(Request $request, Closure $next, ...$levels)
    {
        $adminId = session('admin_id');
        $adminLevel = session('admin_level');

        if (!$adminId) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        if (empty($levels)) {
            return $next($request);
        }

        if (!in_array($adminLevel, $levels)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}