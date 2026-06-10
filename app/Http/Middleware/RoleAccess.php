<?php

namespace App\Http\Middleware;

use App\Support\AdminRole;
use Closure;
use Illuminate\Http\Request;

class RoleAccess
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!session()->has('admin_id')) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        if (empty($permissions)) {
            return $next($request);
        }

        if (AdminRole::superadmin()) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            $permission = strtolower(trim($permission));

            if (AdminRole::is($permission) || AdminRole::can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak. Level admin Anda tidak memiliki izin membuka halaman ini.');
    }
}
