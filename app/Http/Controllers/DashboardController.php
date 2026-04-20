<?php

namespace App\Http\Controllers;

use App\Models\DriverUnitAssignment;
use App\Models\Location;
use App\Models\Menu;
use App\Models\TraccarRequestLog;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
            'traccarEndpoint' => url('/api/location'),
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

        return view('traccar-dashboard', [
            'deviceSummaries' => $deviceSummaries,
            'latestEntries' => $latestEntries,
            'latestDistinctDriverEntries' => $latestDistinctDriverEntries,
            'latestEntriesByDriver' => $latestEntriesByDriver,
            'unknownDeviceCount' => $unknownDeviceCount,
            'latestTrackedLocation' => $latestEntries->first(),
            'requestLogs' => $requestLogs,
        ]);
    }

    public function assignments(Request $request): View
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $drivers = User::query()
            ->where('role', 'driver')
            ->orderBy('name')
            ->with('activeDriverAssignment')
            ->get();

        $units = Unit::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Unit $unit): array => $this->transformUnitForDashboard($unit));

        $activeAssignments = DriverUnitAssignment::query()
            ->with(['driver', 'unit', 'assignedBy'])
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->latest('assigned_at')
            ->get();

        return view('dashboard-assignments', [
            'drivers' => $drivers,
            'units' => $units,
            'activeAssignments' => $activeAssignments,
        ]);
    }

    public function manageResources(Request $request): View
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $drivers = User::query()
            ->where('role', 'driver')
            ->latest('id')
            ->with(['activeDriverAssignment.unit'])
            ->paginate(6, ['*'], 'driver_page')
            ->withQueryString();

        $units = Unit::query()
            ->latest('id')
            ->with([
                'assignments' => fn ($query) => $query->with('driver')->latest('assigned_at'),
            ])
            ->paginate(6, ['*'], 'unit_page')
            ->withQueryString();

        return view('dashboard-manage', [
            'drivers' => $drivers,
            'units' => $units,
        ]);
    }

    public function menus(Request $request): View
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $menus = Menu::query()
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('dashboard-menus', [
            'menus' => $menus,
        ]);
    }

    public function storeMenu(Request $request): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        Menu::query()->create($this->extractMenuPayload($request));

        return $this->redirectWithDashboardStatus($request, 'Menu baru berhasil ditambahkan.');
    }

    public function updateMenu(Request $request, Menu $menu): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $menu->update($this->extractMenuPayload($request, $menu));

        return $this->redirectWithDashboardStatus($request, 'Data menu berhasil diperbarui.');
    }

    public function destroyMenu(Request $request, Menu $menu): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $this->deleteManagedMenuImage($menu->image_path);

        $menuName = $menu->name;
        $menu->delete();

        return $this->redirectWithDashboardStatus($request, "Menu {$menuName} berhasil dihapus.");
    }

    public function storeDriver(Request $request): RedirectResponse
    {
        $owner = $request->user();

        if (! $this->isOwner($owner)) {
            abort(403);
        }

        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'device_id' => ['required', 'string', 'max:120', 'unique:users,device_id'],
            'password' => ['required', 'string', 'min:8'],
        ])->validateWithBag('driverForm');

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'driver',
            'device_id' => $validated['device_id'],
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        return $this->redirectWithDashboardStatus($request, 'Akun driver baru berhasil dibuat.');
    }

    public function updateDriver(Request $request, User $user): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        if (! $user->isDriver()) {
            return $this->redirectWithDashboardStatus($request, 'User yang dipilih bukan akun driver.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'device_id' => ['required', 'string', 'max:120', 'unique:users,device_id,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'device_id' => $validated['device_id'],
            'is_active' => $request->boolean('is_active'),
            'role' => 'driver',
        ];

        if (! blank($validated['password'] ?? null)) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return $this->redirectWithDashboardStatus($request, 'Data driver berhasil diperbarui.');
    }

    public function destroyDriver(Request $request, User $user): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        if (! $user->isDriver()) {
            return $this->redirectWithDashboardStatus($request, 'User yang dipilih bukan akun driver.');
        }

        $hasActiveAssignment = DriverUnitAssignment::query()
            ->where('driver_id', $user->id)
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->exists();

        if ($hasActiveAssignment) {
            return $this->redirectWithDashboardStatus($request, 'Driver masih memiliki assignment aktif. Selesaikan dulu assignment-nya.');
        }

        $driverName = $user->name;
        $user->delete();

        return $this->redirectWithDashboardStatus($request, "Driver {$driverName} berhasil dihapus.");
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'unique:units,code'],
            'status' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ])->validateWithBag('unitForm');

        Unit::query()->create($validated);

        return $this->redirectWithDashboardStatus($request, 'Gerobak baru berhasil ditambahkan.');
    }

    public function updateUnit(Request $request, Unit $unit): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'unique:units,code,'.$unit->id],
            'status' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $unit->update($validated);

        return $this->redirectWithDashboardStatus($request, 'Data gerobak berhasil diperbarui.');
    }

    public function destroyUnit(Request $request, Unit $unit): RedirectResponse
    {
        if (! $this->isOwner($request->user())) {
            abort(403);
        }

        $hasActiveAssignment = DriverUnitAssignment::query()
            ->where('unit_id', $unit->id)
            ->where('status', 'active')
            ->whereNull('ended_at')
            ->exists();

        if ($hasActiveAssignment) {
            return $this->redirectWithDashboardStatus($request, 'Gerobak masih memiliki assignment aktif. Selesaikan dulu assignment-nya.');
        }

        $unitName = $unit->name;
        $unit->delete();

        return $this->redirectWithDashboardStatus($request, "Gerobak {$unitName} berhasil dihapus.");
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
            return $this->redirectWithDashboardStatus($request, 'User yang dipilih bukan akun driver.');
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

        return $this->redirectWithDashboardStatus(
            $request,
            "{$driver->name} sekarang ditugaskan ke {$unit->name}."
        );
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

        return $this->redirectWithDashboardStatus($request, 'Assignment driver berhasil diselesaikan.');
    }

    private function extractMenuPayload(Request $request, ?Menu $existingMenu = null): array
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'tags_input' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ])->validate();

        $tags = collect(explode(',', (string) ($validated['tags_input'] ?? '')))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $currentImagePath = $existingMenu?->image_path;
        $manualImagePath = blank($validated['image_path'] ?? null)
            ? null
            : trim((string) $validated['image_path']);

        $resolvedImagePath = $currentImagePath;

        if ($request->hasFile('image_file')) {
            $storedPath = $request->file('image_file')->store('menus', 'public');
            $resolvedImagePath = 'storage/'.$storedPath;
            $this->deleteManagedMenuImage($currentImagePath);
        } elseif ($request->boolean('remove_image')) {
            $resolvedImagePath = null;
            $this->deleteManagedMenuImage($currentImagePath);
        } elseif ($manualImagePath !== null) {
            if ($currentImagePath !== null && $manualImagePath !== $currentImagePath) {
                $this->deleteManagedMenuImage($currentImagePath);
            }

            $resolvedImagePath = $manualImagePath;
        } elseif ($existingMenu === null) {
            $resolvedImagePath = null;
        }

        return [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => (int) $validated['price'],
            'description' => blank($validated['description'] ?? null) ? null : $validated['description'],
            'image_path' => $resolvedImagePath,
            'tags' => $tags,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function deleteManagedMenuImage(?string $imagePath): void
    {
        if (blank($imagePath) || ! Str::startsWith((string) $imagePath, 'storage/menus/')) {
            return;
        }

        $relativeStoragePath = Str::after((string) $imagePath, 'storage/');
        Storage::disk('public')->delete($relativeStoragePath);
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
                ->where('device_id', $driver->device_id)
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
            ->where('device_id', $activeAssignment?->driver?->device_id)
            ->latest('recorded_at')
            ->first();

        return [
            'id' => $unit->id,
            'name' => $unit->name,
            'code' => $unit->code,
            'device_id' => $activeAssignment?->driver?->device_id,
            'status' => $unit->status,
            'notes' => $unit->notes,
            'active_assignment' => $activeAssignment,
            'latest_location' => $latestLocation,
        ];
    }

    private function redirectWithDashboardStatus(Request $request, string $message): RedirectResponse
    {
        $target = $request->input('redirect_to');

        if (in_array($target, ['dashboard', 'dashboard.assignments.index', 'dashboard.menus.index', 'dashboard.manage.index'], true)) {
            $routeParameters = [];

            if ($target === 'dashboard.manage.index') {
                $driverPage = $request->input('driver_page');
                $unitPage = $request->input('unit_page');

                if (! blank($driverPage)) {
                    $routeParameters['driver_page'] = $driverPage;
                }

                if (! blank($unitPage)) {
                    $routeParameters['unit_page'] = $unitPage;
                }
            }

            return redirect()
                ->route($target, $routeParameters)
                ->with('dashboard_status', $message);
        }

        return redirect()
            ->route('dashboard')
            ->with('dashboard_status', $message);
    }
}
