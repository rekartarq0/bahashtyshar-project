<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settings = Settings::firstOrCreate(
            ['id' => 1],
            [
                'name' => '',
                'address' => '',
                'phone1' => '',
                'phone2' => '',
                'title1' => '',
                'title2' => '',
                'note' => '',
                'image' => '',
            ]
        );
        return array_merge(parent::share($request), [
            'user' => $request->user() ? [
                'roles' => $request->user()->roles->pluck('name'),
                'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name'),
            ] : null,
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'data' => fn() => $request->session()->get('data'),
            ],
            'settings' => $settings
        ]);
    }
}
