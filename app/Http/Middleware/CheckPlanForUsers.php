<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanForUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('tenant.type') === 'individual') {
            return redirect()->route('tenant.dashboard', ['tenant' => session('tenant_domain')])
                ->with('plan_warning', 'Para acessar a gestão de usuários, atualize para o plano Clínicas.');
        }
        return $next($request);
    }
}
