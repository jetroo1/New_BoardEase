<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'tenant') {
            if (auth()->check()) {
                $role = auth()->user()->role;
                return redirect()->route($role === 'owner' ? 'owner.dashboard' : 'admin.dashboard')
                    ->with('error', 'Access denied. Tenant area only.');
            }
            return redirect()->route('login');
        }
        return $next($request);
    }
}