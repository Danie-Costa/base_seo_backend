<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $userRole = auth()->user()->role;

        // Hierarquia de permissões
        $hierarchy = [
            'admin'   => ['admin', 'manager', 'cashier'],
            'manager' => ['manager', 'cashier'],
            'cashier' => ['cashier']
        ];

        foreach ($roles as $role) {
            if (in_array($userRole, $hierarchy[$role] ?? [])) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
