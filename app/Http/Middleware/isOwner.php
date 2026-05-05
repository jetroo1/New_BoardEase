<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'owner') {
            if (auth()->check()) {
                $role = auth()->user()->role;
                return redirect()->route($role === 'admin' ? 'admin.dashboard' : 'search')
                    ->with('error', 'Access denied. Owner area only.');
            }
            return redirect()->route('login');
        }
        return $next($request);
    }
}