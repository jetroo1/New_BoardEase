<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            if (auth()->check()) {
                $role = auth()->user()->role;
                return redirect()->route($role === 'owner' ? 'owner.dashboard' : 'search')
                    ->with('error', 'Access denied. Admin area only.');
            }
            return redirect()->route('login');
        }
        return $next($request);
    }
}