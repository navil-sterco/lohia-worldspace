<?php

namespace App\Http\Middleware;

use App\Models\Module;
use App\Services\ModulePermissionService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Schema;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'permissions' => fn () => $request->user()
                    ? $request->user()->getAllPermissions()->pluck('name')->values()->all()
                    : [],
            ],
            'appLogo' => asset('assets/img/logo.svg'),
            'appUrl' => env('APP_URL'),
            'webUrl' => env('WEB_URL'),
            'tinyMceApiKey' => env('TINY_MCE_API_KEY'),
            'modulesForSidebar' => function () use ($request) {
                if (! Schema::hasTable('modules')) {
                    return [];
                }

                $user = $request->user();

                return Module::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug'])
                    ->filter(function (Module $m) use ($user) {
                        if (! $user) {
                            return false;
                        }

                        return $user->can(
                            ModulePermissionService::entryPermission($m->slug, 'entries.view')
                        );
                    })
                    ->map(fn ($m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                        'slug' => $m->slug,
                    ])
                    ->values();
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
        ];
    }
}
