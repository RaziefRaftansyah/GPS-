<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\DriverUnitAssignment;
use App\Models\Location;
use App\Models\TraccarRequestLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TraccarController extends BaseDashboardController
{
    public function index(Request $request): View
    {
        $this->abortUnlessOwner($request->user());

        $latestEntries = Location::query()
            ->latest('recorded_at')
            ->limit(25)
            ->get();

        $driversByDeviceId = User::query()
            ->where('role', 'driver')
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->get()
            ->keyBy(fn (User $driver): string => (string) $driver->device_id);

        $latestDistinctDriverEntries = Location::query()
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->latest('recorded_at')
            ->get()
            ->map(function (Location $location) use ($driversByDeviceId): ?array {
                $driver = $driversByDeviceId->get((string) $location->device_id);

                if (! $driver) {
                    return null;
                }

                return [
                    'entry' => $location,
                    'driver_id' => $driver->id,
                    'driver_name' => $driver->name,
                    'device_id' => $location->device_id,
                    'recorded_at' => $location->recorded_at,
                ];
            })
            ->filter()
            ->unique('driver_id')
            ->take(5)
            ->values();

        $latestEntriesByDriver = Location::query()
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->latest('recorded_at')
            ->get()
            ->groupBy(fn (Location $location): string => (string) $location->device_id)
            ->map(function (Collection $locations, string $deviceId) use ($driversByDeviceId): array {
                $driver = $driversByDeviceId->get($deviceId);

                return [
                    'device_id' => $deviceId,
                    'driver_id' => $driver?->id,
                    'driver_name' => $driver?->name,
                    'entries' => $locations->take(5)->values(),
                    'latest_recorded_at' => $locations->first()?->recorded_at,
                ];
            })
            ->sortByDesc('latest_recorded_at')
            ->values();

        $activeAssignmentsByDriver = DriverUnitAssignment::query()
            ->with(['driver', 'unit'])
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->latest('assigned_at')
            ->get()
            ->unique('driver_id')
            ->keyBy('driver_id');

        $deviceSummaries = Location::query()
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->latest('recorded_at')
            ->get()
            ->groupBy('device_id')
            ->map(function (Collection $locations, string $deviceId) use ($driversByDeviceId, $activeAssignmentsByDriver): array {
                $latest = $locations->first();
                $driver = $driversByDeviceId->get($deviceId);
                $assignment = $driver ? $activeAssignmentsByDriver->get($driver->id) : null;
                $unit = $assignment?->unit;

                return [
                    'device_id' => $deviceId,
                    'unit_name' => $unit?->name,
                    'unit_code' => $unit?->code,
                    'driver_name' => $driver?->name,
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

        return view('dashboard.owner.traccar.index', [
            'deviceSummaries' => $deviceSummaries,
            'latestEntries' => $latestEntries,
            'latestDistinctDriverEntries' => $latestDistinctDriverEntries,
            'latestEntriesByDriver' => $latestEntriesByDriver,
            'unknownDeviceCount' => $unknownDeviceCount,
            'latestTrackedLocation' => $latestEntries->first(),
            'requestLogs' => $requestLogs,
        ]);
    }
}
