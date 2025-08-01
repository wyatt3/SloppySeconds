<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnsMeal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ?\App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Meal $meal */
        $meal = $request->route('meal');

        if ($meal->user_group_id !== $user?->user_group_id) {
            return abort(Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
