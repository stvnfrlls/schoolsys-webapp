<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class SidebarService
{
    public static function getMenu()
    {
        $menu = config('sidebar');

        return collect($menu)
            ->map(function ($group) {
                $group['items'] = collect($group['items'])
                    ->filter(function ($item) {
                        // If no permission required, show it
                        if (!$item['permission']) {
                            return true;
                        }

                        /** @var \App\Models\User $authUser */
                        $authUser = Auth::user();

                        // Otherwise check if user has permission
                        return $authUser->can($item['permission']);
                    })
                    ->values()
                    ->toArray();

                return $group;
            })
            // Filter out empty groups (optional)
            ->filter(function ($group) {
                return !empty($group['items']);
            })
            ->values()
            ->toArray();
    }
}
