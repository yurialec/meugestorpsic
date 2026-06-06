<?php

namespace App\Http\Middleware;

use App\Models\Admin\Subscription;
use Auth;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTenantSub
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('tenant.subscription.*')) {
            return $next($request);
        }

        $sub = Subscription::where('tenant_id', session('tenant_id'))->first();

        if (!$sub) {
            return redirect()->route('tenant.subscription.index', ['tenant' => session('tenant_domain')]);
        }

        if (Carbon::parse($sub->current_period_end)->isPast()) {
            return redirect()->route('tenant.subscription.index', ['tenant' => session('tenant_domain')]);
        }

        return $next($request);
    }
}
