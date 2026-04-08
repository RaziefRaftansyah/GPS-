<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\TraccarRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load([
            'purchases' => fn ($query) => $query->latest('purchased_at'),
        ]);

        $purchaseCount = $user->purchases->count();
        $totalSpent = $user->purchases->sum('total_price');
        $favoriteMenu = $user->purchases
            ->groupBy('menu_name')
            ->sortByDesc(fn ($purchases) => $purchases->count())
            ->keys()
            ->first();

        return view('dashboard', [
            'user' => $user,
            'purchases' => $user->purchases,
            'purchaseCount' => $purchaseCount,
            'totalSpent' => $totalSpent,
            'favoriteMenu' => $favoriteMenu,
        ]);
    }

    public function traccar(): View
    {
        $latestEntries = Location::query()
            ->latest('recorded_at')
            ->limit(25)
            ->get();

        $deviceSummaries = Location::query()
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->latest('recorded_at')
            ->get()
            ->groupBy('device_id')
            ->map(function (Collection $locations, string $deviceId): array {
                $latest = $locations->first();

                return [
                    'device_id' => $deviceId,
                    'last_seen' => $latest?->recorded_at,
                    'latitude' => $latest?->latitude,
                    'longitude' => $latest?->longitude,
                    'battery_level' => $latest?->battery_level,
                    'is_moving' => $latest?->is_moving,
                    'activity' => $latest?->activity,
                    'event_type' => $latest?->event_type,
                    'total_logs' => $locations->count(),
                ];
            })
            ->sortByDesc('last_seen')
            ->values();

        $unknownDeviceCount = Location::query()
            ->whereNull('device_id')
            ->orWhere('device_id', '')
            ->count();

        $requestLogs = TraccarRequestLog::query()
            ->latest()
            ->limit(20)
            ->get();

        return view('traccar-dashboard', [
            'deviceSummaries' => $deviceSummaries,
            'latestEntries' => $latestEntries,
            'unknownDeviceCount' => $unknownDeviceCount,
            'latestTrackedLocation' => $latestEntries->first(),
            'requestLogs' => $requestLogs,
        ]);
    }
}
