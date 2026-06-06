<?php

namespace App\Http\Middleware;

use App\Models\Tenants\Tenant;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\RedirectController;
use Symfony\Component\HttpFoundation\Response;

class AuthTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $urlTenant = $request->route('tenant');

        if ($urlTenant !== session('tenant_domain')) {
            return redirect()->route('index.site');
        }

        return $next($request);
    }
}
