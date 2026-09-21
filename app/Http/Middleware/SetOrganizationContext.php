<?php

namespace App\Http\Middleware;

use App\Contexts\OrganizationContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetOrganizationContext
{
    public function __construct(
        private OrganizationContext $organizationContext
    ) {}
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $this->organizationContext->set(
                $user->organization
            );
        }

        return $next($request);
    }
}
