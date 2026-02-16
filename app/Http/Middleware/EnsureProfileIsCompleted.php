<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Check for student profile
            if ($user->account_type === 'student' && !$user->exam_purpose) {
                if (!$request->routeIs('auth.form.student') && !$request->routeIs('auth.form.student.post') && !$request->routeIs('auth.logout')) {
                    return redirect()->route('auth.form.student')->with('info', 'Please complete your student profile.');
                }
            }

            // Check for agent profile
            if (in_array($user->account_type, ['reseller_agent', 'agent']) && !$user->business_name) {
                if (!$request->routeIs('auth.forms.B2BResellerAgent') && !$request->routeIs('auth.form.agent.post') && !$request->routeIs('auth.logout')) {
                    return redirect()->route('auth.forms.B2BResellerAgent')->with('info', 'Please complete your agent profile.');
                }
            }
        }

        return $next($request);
    }
}
