<?php

namespace App\Http\Middleware;

use App\Models\Module;
use App\Services\ModulePermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModulePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $module = $request->route('module');

        if (!$module instanceof Module) {
            abort(403);
        }

        $permission = ModulePermissionService::entryPermission($module->slug, $ability);

        if (!$user->can($permission)) {
            abort(403, 'You do not have permission to perform this action on this module.');
        }

        return $next($request);
    }
}
