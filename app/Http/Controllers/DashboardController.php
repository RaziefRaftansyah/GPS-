<?php

namespace App\Http\Controllers;

use App\Models\DriverUnitAssignment;
use App\Models\Location;
use App\Models\TraccarRequestLog;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->isDriver()) {
            return $this->driverDashboard($user);
        }

        if (! $this->isOwner($user)) {
            return redirect()->route('profile.edit');
        }

        $activeThreshold = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;
        $currentSessionId = $request->session()->getId();

        $activeSessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id')
            ->where('sessions.last_activity', '>=', $activeThreshold)
            ->orderByDesc('sessions.last_activity')
            ->get([
                'sessions.id as session_id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name',
                'users.email',
            ]);

        $activeUsers = $activeSessions
            ->groupBy('user_id')
            ->map(function (Collection $sessions) use ($currentSessionId): array {
                $latestSession = $sessions->first();

                return [
                    'user_id' => (int) $latestSession->user_id,
                    'name' => $latestSession->name,
                    'email' => $latestSession->email,
                    'active_sessions' => $sessions->count(),
                    'last_seen' => Carbon::createFromTimestamp((int) $latestSession->last_activity),
                    'ip_address' => $latestSession->ip_address ?: 'Tidak terdeteksi',
                    'user_agent' => Str::limit($latestSession->user_agent ?: 'Browser tidak terdeteksi', 72),
                    'is_current_admin' => $sessions->contains(
                        fn (object $session): bool => $session->session_id === $currentSessionId
                    ),
                ];
            })
            ->sortByDesc('last_seen')
            ->values();

        $units = Unit::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Unit $unit): array => $this->transformUnitForDashboard($unit));

        $drivers = User::query()
            ->where('role', 'driver')
            ->orderBy('name')
            ->with('activeDriverAssignment')
            ->get();

        $activeAssignments = DriverUnitAssignment::query()
            ->with(['driver', 'unit', 'assignedBy'])
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->latest('assigned_at')
            ->get();

        return view('dashboard', [
            'activeUsers' => $activeUsers,
            'activeUserCount' => $activeUsers->count(),
            'activeSessionCount' => $activeSessions->count(),
            'latestLoginAt' => $activeUsers->first()['last_seen'] ?? null,
            'adminEmail' => $this->adminEmail(),
            'units' => $units,
            'unitCount' => $units->count(),
            'assignedUnitCount' => $units->where('active_assignment', '!=', null)->count(),
            'drivers' => $drivers,
            'driverCount' => $drivers->count(),
            'availableDrivers' => $drivers->filter(fn (User $driver) => $driver->activeDriverAssignment === null)->values(),
            'activeAssignments' => $activeAssignments,
        ]);
    }

    public function kickUser(Request $request, User $user): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        if ((int) $request->user()->id === (int) $user->id) {
            return redirect()
                ->route('dashboard')
                ->with('dashboard_status', 'Akun admin utama tidak bisa di-kick dari dashboard ini.');
        }

        $deletedSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        $message = $deletedSessions > 0
            ? "{$user->name} berhasil dikeluarkan dari {$deletedSessions} sesi aktif."
            : "{$user->name} sedang tidak login, jadi tidak ada sesi yang dihapus.";

        return redirect()
            ->route('dashboard')
            ->with('dashboard_status', $message);
    }

    public function traccar(): View
    {
        if (! $this->isOwner(request()->user())) {
            abort(403);
        }

        $latestEntries = Location::query()
            ->latest('recorded_at')
            ->limit(25)
            ->get();

        $unitsByDeviceId = Unit::query()
            ->whereNotNull('device_id')
            ->get()
            ->keyBy(fn (Unit $unit): string => (string) $unit->device_id);

        $activeAssignmentsByUnit = DriverUnitAssignment::query()
            ->with('driver')
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->latest('assigned_at')
            ->get()
            ->unique('unit_id')
            ->keyBy('unit_id');

        $deviceSummaries = Location::query()
            ->whereNotNull('device_id')
            ->where('device_id', '!=', '')
            ->latest('recorded_at')
            ->get()
            ->groupBy('device_id')
            ->map(function (Collection $locations, string $deviceId) use ($unitsByDeviceId, $activeAssignmentsByUnit): array {
                $latest = $locations->first();
                $unit = $unitsByDeviceId->get($deviceId);
                $assignment = $unit ? $activeAssignmentsByUnit->get($unit->id) : null;

                return [
                    'device_id' => $deviceId,
                    'unit_name' => $unit?->name,
                    'unit_code' => $unit?->code,
                    'driver_name' => $assignment?->driver?->name,
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

    public function storeDriver(Request $request): RedirectResponse
    {
        $owner = $request->user();

        if (! $this->isOwner($owner)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'driver',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('dashboard_status', 'Akun driver baru berhasil dibuat.');
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'unique:units,code'],
            'device_id' => ['required', 'string', 'max:120', 'unique:units,device_id'],
            'status' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Unit::query()->create($validated);

        return redirect()
            ->route('dashboard')
            ->with('dashboard_status', 'Gerobak baru berhasil ditambahkan.');
    }

    public function assignDriver(Request $request): RedirectResponse
    {
        $owner = $request->user();

        if (! $this->isOwner($owner)) {
            abort(403);
        }

        $validated = $request->validate([
            'driver_id' => ['required', 'integer', 'exists:users,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $driver = User::query()->findOrFail($validated['driver_id']);
        $unit = Unit::query()->findOrFail($validated['unit_id']);

        if (! $driver->isDriver()) {
            return redirect()
                ->route('dashboard')
                ->with('dashboard_status', 'User yang dipilih bukan akun driver.');
        }

        $now = now();

        DriverUnitAssignment::query()
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->where(function ($query) use ($driver, $unit): void {
                $query->where('driver_id', $driver->id)
                    ->orWhere('unit_id', $unit->id);
            })
            ->update([
                'status' => 'completed',
                'ended_at' => $now,
            ]);

        DriverUnitAssignment::query()->create([
            'driver_id' => $driver->id,
            'unit_id' => $unit->id,
            'assigned_by' => $owner?->id,
            'assigned_at' => $now,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('dashboard_status', "{$driver->name} sekarang ditugaskan ke {$unit->name}.");
    }

    public function finishAssignment(Request $request, DriverUnitAssignment $assignment): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        if ($assignment->status === 'active' && $assignment->ended_at === null) {
            $assignment->update([
                'status' => 'completed',
                'ended_at' => now(),
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('dashboard_status', 'Assignment driver berhasil diselesaikan.');
    }

    private function adminEmail(): string
    {
        return (string) env('ADMIN_EMAIL', 'admin@kopikeliling.com');
    }

    private function isOwner(?User $user): bool
    {
        return $user !== null
            && ($user->isOwner() || $user->email === $this->adminEmail());
    }

    private function driverDashboard(User $driver): View
    {
        $driver->load(['activeDriverAssignment.unit']);

        $assignment = $driver->activeDriverAssignment;
        $unit = $assignment?->unit;
        $latestLocation = null;

        if ($unit !== null) {
            $latestLocation = Location::query()
                ->where('device_id', $unit->device_id)
                ->latest('recorded_at')
                ->first();
        }

        return view('driver-dashboard', [
            'driver' => $driver,
            'assignment' => $assignment,
            'unit' => $unit,
            'latestLocation' => $latestLocation,
            'recentAssignments' => $driver->driverAssignments()
                ->with('unit')
                ->latest('assigned_at')
                ->limit(5)
                ->get(),
        ]);
    }

    private function transformUnitForDashboard(Unit $unit): array
    {
        $unit->loadMissing([
            'assignments' => fn ($query) => $query->with('driver')->latest('assigned_at'),
        ]);

        $activeAssignment = $unit->assignments
            ->first(fn (DriverUnitAssignment $assignment): bool => $assignment->status === 'active' && $assignment->ended_at === null);

        $latestLocation = Location::query()
            ->where('device_id', $unit->device_id)
            ->latest('recorded_at')
            ->first();

        return [
            'id' => $unit->id,
            'name' => $unit->name,
            'code' => $unit->code,
            'device_id' => $unit->device_id,
            'status' => $unit->status,
            'notes' => $unit->notes,
            'active_assignment' => $activeAssignment,
            'latest_location' => $latestLocation,
        ];
    }
}
