<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventDemoWrites
{
    /**
     * Blocks any write action (POST, PUT, PATCH, DELETE) when the app
     * is running in demo mode. Controlled entirely by the DEMO_MODE
     * environment variable - has zero effect unless explicitly set,
     * so it never impacts the local/installer version of the app.
     */
    public function handle(Request $request, Closure $next)
    {
        if (env('DEMO_MODE') && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {

            // Allow login/logout to still work normally - only block
            // actual data-modifying actions within the app itself
            $allowedPaths = ['login', 'logout', 'user/login', 'user/logout'];
            foreach ($allowedPaths as $path) {
                if ($request->is($path)) {
                    return $next($request);
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'This is a live demo. Changes are not saved.'
                ], 403);
            }

            return back()->with('demo_blocked', 'This is a live demo. Changes are not saved here, feel free to explore though!');
        }

        return $next($request);
    }
}
