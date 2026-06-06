<?php

namespace App\Http\Middleware;

use App\Contracts\Audit\AuditLogger;
use App\Enums\AuditEventType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLoginMiddleware
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $previousUser = $this->authenticatedUser();

        $response = $next($request);

        if ($routeName && in_array($routeName, config('audit.logout_routes', []), true)) {
            $this->auditLogger->log(AuditEventType::Logout, [
                'user' => $previousUser,
                'metadata' => ['route' => $routeName],
            ], $request);
        }

        if (
            $request->isMethod('post')
            && $routeName
            && in_array($routeName, config('audit.login_routes', []), true)
        ) {
            $currentUser = $this->authenticatedUser();
            $eventType = $currentUser ? AuditEventType::LoginSuccess : AuditEventType::LoginFailed;

            $this->auditLogger->log($eventType, [
                'user' => $currentUser,
                'user_email' => $currentUser?->email ?? $request->input('email'),
                'metadata' => [
                    'route' => $routeName,
                    'guard' => $this->authenticatedGuard(),
                    'status_code' => $response->getStatusCode(),
                ],
            ], $request);
        }

        return $response;
    }

    private function authenticatedUser(): mixed
    {
        foreach (['web', 'client', 'employee'] as $guard) {
            if (auth()->guard($guard)->check()) {
                return auth()->guard($guard)->user();
            }
        }

        return null;
    }

    private function authenticatedGuard(): ?string
    {
        foreach (['web', 'client', 'employee'] as $guard) {
            if (auth()->guard($guard)->check()) {
                return $guard;
            }
        }

        return null;
    }
}
