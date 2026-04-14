<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\DriverUnitAssignment;
use App\Models\TraccarRequestLog;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = $this->recentLocations();
        $activeUnits = $this->activeUnitLocations();

        return view('tracker', [
            'locations' => $locations,
            'activeUnits' => $activeUnits,
            'latestLocation' => $activeUnits->last() ?? $locations->last(),
            'traccarEndpoint' => url('/api/location'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->normalizePayload($request);
        $log = $this->createRequestLog($request, $payload);

        $validator = Validator::make($payload, [
            'device_id' => ['nullable', 'string', 'max:120'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'speed' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'between:0,360'],
            'altitude' => ['nullable', 'numeric'],
            'battery_level' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_charging' => ['nullable', 'boolean'],
            'is_moving' => ['nullable', 'boolean'],
            'activity' => ['nullable', 'string', 'max:100'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            $log->update([
                'processed' => false,
                'error_message' => json_encode($validator->errors()->toArray()),
            ]);

            return response()->json([
                'message' => 'Location payload is invalid.',
                'errors' => $validator->errors(),
                'received' => $payload,
                'log_id' => $log->id,
            ], 422);
        }

        $validated = $validator->validated();

        $location = Location::query()->create([
            ...Arr::except($validated, ['recorded_at']),
            'recorded_at' => $this->parseTimestamp($validated['recorded_at'] ?? null),
        ]);

        $log->update([
            'processed' => true,
            'location_id' => $location->id,
        ]);

        $unit = null;
        $assignment = null;

        if ($location->device_id) {
            $driver = User::query()
                ->where('role', 'driver')
                ->where('device_id', $location->device_id)
                ->first();

            if ($driver !== null) {
                $assignment = DriverUnitAssignment::query()
                    ->with(['driver', 'unit'])
                    ->where('driver_id', $driver->id)
                    ->where('status', 'active')
                    ->whereNull('ended_at')
                    ->latest('assigned_at')
                    ->first();

                $unit = $assignment?->unit;
            }
        }

        return response()->json([
            'message' => 'Location saved successfully.',
            'location' => $this->transformLocation($location, $unit, $assignment),
            'log_id' => $log->id,
        ], 201);
    }

    public function latest(): JsonResponse
    {
        $locations = $this->recentLocations();
        $activeUnits = $this->activeUnitLocations();

        return response()->json([
            'latest' => $activeUnits->last() ?? $locations->last(),
            'locations' => $locations,
            'active_units' => $activeUnits,
            'active_unit_count' => $activeUnits->count(),
        ]);
    }

    public function browserTest(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'API location siap dipakai dari browser atau Traccar.',
            'server_url' => url('/api/location'),
            'latest_url' => url('/api/location/latest'),
            'browser_test_send_url' => url('/api/location/test-send').'?id=gerobak-kopi-01&lat=-5.147665&lon=119.432732&batt=88&timestamp='.now()->timestamp,
            'your_ip' => '172.16.239.62',
            'query_format' => [
                'required' => ['id', 'lat', 'lon'],
                'optional' => ['timestamp', 'accuracy', 'speed', 'bearing', 'altitude', 'batt', 'charge', 'moving', 'activity', 'event'],
            ],
            'received_query' => $request->query(),
        ]);
    }

    public function browserTestSend(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    protected function recentLocations(): Collection
    {
        $locations = Location::query()
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->latest('recorded_at')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        $deviceIds = $locations
            ->pluck('device_id')
            ->filter()
            ->unique()
            ->values();

        $driversByDeviceId = User::query()
            ->where('role', 'driver')
            ->whereIn('device_id', $deviceIds)
            ->get()
            ->keyBy('device_id');

        $activeAssignments = DriverUnitAssignment::query()
            ->with(['driver', 'unit'])
            ->whereIn('driver_id', $driversByDeviceId->pluck('id'))
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->latest('assigned_at')
            ->get()
            ->unique('driver_id')
            ->keyBy('driver_id');

        return $locations->map(function (Location $location) use ($driversByDeviceId, $activeAssignments): array {
            $driver = $driversByDeviceId->get($location->device_id);
            $assignment = $driver ? $activeAssignments->get($driver->id) : null;
            $unit = $assignment?->unit;

            return $this->transformLocation($location, $unit, $assignment);
        });
    }

    protected function transformLocation(
        Location $location,
        ?Unit $unit = null,
        ?DriverUnitAssignment $assignment = null
    ): array
    {
        return [
            'id' => $location->id,
            'device_id' => $location->device_id,
            'unit_name' => $unit?->name,
            'unit_code' => $unit?->code,
            'driver_name' => $assignment?->driver?->name,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'accuracy' => $location->accuracy,
            'speed' => $location->speed,
            'heading' => $location->heading,
            'altitude' => $location->altitude,
            'battery_level' => $location->battery_level,
            'is_charging' => $location->is_charging,
            'is_moving' => $location->is_moving,
            'activity' => $location->activity,
            'event_type' => $location->event_type,
            'recorded_at' => optional($location->recorded_at)
                ?->timezone(config('app.timezone'))
                ->format('Y-m-d H:i:s'),
        ];
    }

    protected function activeUnitLocations(): Collection
    {
        $activeAssignments = DriverUnitAssignment::query()
            ->with(['driver', 'unit'])
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->latest('assigned_at')
            ->get()
            ->unique('driver_id');

        return $activeAssignments
            ->map(function (DriverUnitAssignment $assignment): ?array {
                $driver = $assignment->driver;

                if ($driver === null || blank($driver->device_id)) {
                    return null;
                }

                $location = Location::query()
                    ->where('device_id', $driver->device_id)
                    ->latest('recorded_at')
                    ->first();

                if ($location === null) {
                    return null;
                }

                return $this->transformLocation($location, $assignment->unit, $assignment);
            })
            ->filter()
            ->sortBy('unit_name')
            ->values();
    }

    protected function normalizePayload(Request $request): array
    {
        $jsonPayload = $request->json()->all();

        if (isset($jsonPayload['location']['coords'])) {
            return [
                'device_id' => Arr::get($jsonPayload, 'device_id'),
                'latitude' => Arr::get($jsonPayload, 'location.coords.latitude'),
                'longitude' => Arr::get($jsonPayload, 'location.coords.longitude'),
                'accuracy' => Arr::get($jsonPayload, 'location.coords.accuracy'),
                'speed' => $this->normalizeNonNegativeNumber(Arr::get($jsonPayload, 'location.coords.speed')),
                'heading' => $this->normalizeNonNegativeNumber(Arr::get($jsonPayload, 'location.coords.heading')),
                'altitude' => Arr::get($jsonPayload, 'location.coords.altitude'),
                'battery_level' => $this->normalizeBatteryLevel(Arr::get($jsonPayload, 'location.battery.level')),
                'is_charging' => Arr::get($jsonPayload, 'location.battery.is_charging'),
                'is_moving' => Arr::get($jsonPayload, 'location.is_moving'),
                'activity' => Arr::get($jsonPayload, 'location.activity.type'),
                'event_type' => Arr::get($jsonPayload, 'location.event'),
                'recorded_at' => Arr::get($jsonPayload, 'location.timestamp'),
            ];
        }

        $location = $request->input('location');
        [$locationLat, $locationLng] = is_string($location) && str_contains($location, ',')
            ? array_map('trim', explode(',', $location, 2))
            : [null, null];

        return [
            'device_id' => $request->input('device_id')
                ?? $request->input('deviceid')
                ?? $request->input('id'),
            'latitude' => $request->input('latitude')
                ?? $request->input('lat')
                ?? $locationLat,
            'longitude' => $request->input('longitude')
                ?? $request->input('lon')
                ?? $locationLng,
            'accuracy' => $request->input('accuracy'),
            'speed' => $this->normalizeNonNegativeNumber($request->input('speed')),
            'heading' => $this->normalizeNonNegativeNumber(
                $request->input('heading')
                    ?? $request->input('bearing')
            ),
            'altitude' => $request->input('altitude'),
            'battery_level' => $this->normalizeBatteryLevel($request->input('batt')),
            'is_charging' => $this->normalizeBoolean($request->input('charge')),
            'is_moving' => $this->normalizeBoolean($request->input('moving')),
            'activity' => $request->input('activity'),
            'event_type' => $request->input('event'),
            'recorded_at' => $request->input('timestamp'),
        ];
    }

    protected function createRequestLog(Request $request, array $payload): TraccarRequestLog
    {
        return TraccarRequestLog::query()->create([
            'method' => $request->method(),
            'path' => $request->path(),
            'content_type' => $request->header('Content-Type'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => Arr::only($request->headers->all(), [
                'content-type',
                'user-agent',
                'accept',
                'host',
                'content-length',
            ]),
            'query_payload' => $request->query(),
            'form_payload' => $request->request->all(),
            'json_payload' => $request->json()->all(),
            'normalized_payload' => $payload,
            'raw_body' => $request->getContent(),
            'processed' => false,
        ]);
    }

    protected function parseTimestamp(mixed $timestamp): Carbon
    {
        if (blank($timestamp)) {
            return now();
        }

        if (is_numeric($timestamp)) {
            $value = (int) $timestamp;

            return (strlen((string) abs($value)) > 10
                ? Carbon::createFromTimestampMs($value)
                : Carbon::createFromTimestamp($value))
                ->timezone(config('app.timezone'));
        }

        return Carbon::parse((string) $timestamp)->timezone(config('app.timezone'));
    }

    protected function normalizeBatteryLevel(mixed $level): ?float
    {
        if ($level === null || $level === '') {
            return null;
        }

        $numeric = (float) $level;

        return $numeric <= 1 ? round($numeric * 100, 2) : round($numeric, 2);
    }

    protected function normalizeBoolean(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    protected function normalizeNonNegativeNumber(mixed $value): int|float|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        $number = $value + 0;

        return $number < 0 ? null : $number;
    }
}
