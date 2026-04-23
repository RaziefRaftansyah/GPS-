<?php

namespace App\Http\Controllers;

use App\Models\DriverAttendanceLog;
use App\Models\DriverUnitAssignment;
use App\Models\Location;
use App\Models\Menu;
use App\Models\TraccarRequestLog;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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
            'driverAttendanceQrLink' => URL::signedRoute('dashboard.driver.attendance.qr', [], null, false),
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
                'checked_out_at' => $now,
            ]);

        DriverUnitAssignment::query()->create([
            'driver_id' => $driver->id,
            'unit_id' => $unit->id,
            'assigned_by' => $owner?->id,
            'assigned_at' => $now,
            'checked_in_at' => null,
            'checked_out_at' => null,
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
                'checked_out_at' => $assignment->checked_out_at ?? now(),
            ]);
        }

        return $this->redirectWithDashboardStatus($request, 'Assignment driver berhasil diselesaikan.');
    }

    public function driverClockIn(Request $request): RedirectResponse
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $assignment = $driver->activeDriverAssignment()->first();

        if (! $assignment) {
            return $this->redirectWithDashboardStatus($request, 'Kamu belum punya assignment aktif, jadi belum bisa absen masuk.');
        }

        if ($assignment->checked_in_at !== null && $assignment->checked_out_at === null) {
            return $this->redirectWithDashboardStatus($request, 'Kamu sudah absen masuk. Lokasi GPS sekarang tampil di peta publik.');
        }

        $assignment->update([
            'checked_in_at' => now(),
            'checked_out_at' => null,
        ]);
        $this->logDriverClockIn($driver, $assignment);

        return $this->redirectWithDashboardStatus($request, 'Absen masuk berhasil. Lokasi GPS kamu sekarang muncul di peta publik.');
    }

    public function driverClockOut(Request $request): RedirectResponse
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $assignment = $driver->activeDriverAssignment()->first();

        if (! $assignment) {
            return $this->redirectWithDashboardStatus($request, 'Kamu belum punya assignment aktif.');
        }

        if ($assignment->checked_in_at === null) {
            return $this->redirectWithDashboardStatus($request, 'Kamu belum absen masuk, jadi tidak bisa absen keluar.');
        }

        if ($assignment->checked_out_at !== null) {
            return $this->redirectWithDashboardStatus($request, 'Kamu sudah absen keluar.');
        }

        $assignment->update([
            'checked_out_at' => now(),
        ]);
        $this->logDriverClockOut($driver, $assignment);

        return $this->redirectWithDashboardStatus($request, 'Absen keluar berhasil. Lokasi GPS kamu disembunyikan dari peta publik.');
    }

    public function driverAttendanceViaQr(Request $request): RedirectResponse|JsonResponse
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $assignment = $driver->activeDriverAssignment()->with('unit')->first();

        if (! $assignment) {
            return $this->attendanceResponse(
                $request,
                false,
                null,
                'Kamu belum punya assignment aktif, jadi QR absensi belum bisa dipakai.',
                'no_assignment',
                'Belum ada assignment aktif.'
            );
        }

        if ($assignment->checked_in_at === null || $assignment->checked_out_at !== null) {
            $assignment->update([
                'checked_in_at' => now(),
                'checked_out_at' => null,
            ]);
            $this->logDriverClockIn($driver, $assignment);

            return $this->attendanceResponse(
                $request,
                true,
                'clock_in',
                'QR berhasil dipindai. Absen masuk tercatat dan lokasi GPS kamu sekarang tampil di peta publik.',
                'clocked_in',
                'Sudah absen masuk.'
            );
        }

        $assignment->update([
            'checked_out_at' => now(),
        ]);
        $this->logDriverClockOut($driver, $assignment);

        return $this->attendanceResponse(
            $request,
            true,
            'clock_out',
            'QR berhasil dipindai. Absen keluar tercatat dan lokasi GPS kamu disembunyikan dari peta publik.',
            'clocked_out',
            'Sudah absen keluar.'
        );
    }

    public function driverProducts(Request $request): View
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $menus = Menu::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $selectedMenuIds = $driver->selectedMenus()
            ->pluck('menus.id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        return view('driver-products', [
            'driver' => $driver,
            'menus' => $menus,
            'selectedMenuIds' => $selectedMenuIds,
        ]);
    }

    public function updateDriverProducts(Request $request): RedirectResponse
    {
        $driver = $request->user();

        if (! $driver?->isDriver()) {
            abort(403);
        }

        $validated = $request->validate([
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer'],
        ]);

        $activeMenuIds = Menu::query()
            ->where('is_active', true)
            ->pluck('id');

        $selectedMenuIds = collect($validated['menu_ids'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->intersect($activeMenuIds)
            ->values();

        $driver->selectedMenus()->sync($selectedMenuIds->all());

        $selectedCount = $selectedMenuIds->count();
        $message = $selectedCount > 0
            ? "Pilihan produk berhasil disimpan ({$selectedCount} menu aktif)."
            : 'Pilihan produk dikosongkan. Tidak ada menu yang dipilih untuk jualan.';

        return redirect()
            ->route('dashboard.driver.products.index')
            ->with('dashboard_status', $message);
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
        $driver->load(['activeDriverAssignment.unit', 'activeDriverAssignment.assignedBy']);

        $assignment = $driver->activeDriverAssignment;
        $unit = $assignment?->unit;
        $isClockedIn = $assignment !== null
            && $assignment->checked_in_at !== null
            && $assignment->checked_out_at === null;
        $isClockedOut = $assignment !== null
            && $assignment->checked_in_at !== null
            && $assignment->checked_out_at !== null;
        $recentLocations = collect();

        if (! blank($driver->device_id)) {
            $recentLocations = Location::query()
                ->where('device_id', $driver->device_id)
                ->latest('recorded_at')
                ->limit(5)
                ->get();
        }

        $latestLocation = $recentLocations->first();
        $locationAgeMinutes = $latestLocation?->recorded_at?->diffInMinutes(now());
        $isLocationFresh = $locationAgeMinutes !== null && $locationAgeMinutes <= 5;
        $shiftStartAt = $isClockedIn ? $assignment?->checked_in_at : null;
        $shiftDurationMinutes = $shiftStartAt?->diffInMinutes(now());

        return view('driver-dashboard', [
            'driver' => $driver,
            'assignment' => $assignment,
            'unit' => $unit,
            'latestLocation' => $latestLocation,
            'recentLocations' => $recentLocations,
            'locationAgeMinutes' => $locationAgeMinutes,
            'isLocationFresh' => $isLocationFresh,
            'isClockedIn' => $isClockedIn,
            'isClockedOut' => $isClockedOut,
            'shiftStartAt' => $shiftStartAt,
            'shiftDurationMinutes' => $shiftDurationMinutes,
            'recentAttendanceLogs' => DriverAttendanceLog::query()
                ->where('user_id', $driver->id)
                ->with('assignment.unit')
                ->latest('clocked_in_at')
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

    private function logDriverClockIn(User $driver, ?DriverUnitAssignment $assignment): void
    {
        if (! $driver->isDriver()) {
            return;
        }

        $assignment?->loadMissing('unit');

        DriverAttendanceLog::query()->create([
            'user_id' => $driver->id,
            'driver_unit_assignment_id' => $assignment?->id,
            'unit_name' => $assignment?->unit?->name,
            'clocked_in_at' => now(),
            'clocked_out_at' => null,
        ]);
    }

    private function logDriverClockOut(User $driver, ?DriverUnitAssignment $assignment): void
    {
        if (! $driver->isDriver()) {
            return;
        }

        $openLogQuery = DriverAttendanceLog::query()
            ->where('user_id', $driver->id)
            ->whereNull('clocked_out_at')
            ->latest('clocked_in_at');

        $openLog = null;

        if ($assignment) {
            $openLog = (clone $openLogQuery)
                ->where('driver_unit_assignment_id', $assignment->id)
                ->first();
        }

        if (! $openLog) {
            $openLog = $openLogQuery->first();
        }

        if ($openLog) {
            $openLog->update([
                'clocked_out_at' => now(),
            ]);

            return;
        }

        $assignment?->loadMissing('unit');

        DriverAttendanceLog::query()->create([
            'user_id' => $driver->id,
            'driver_unit_assignment_id' => $assignment?->id,
            'unit_name' => $assignment?->unit?->name,
            'clocked_in_at' => $assignment?->checked_in_at ?? now(),
            'clocked_out_at' => now(),
        ]);
    }

    private function attendanceResponse(
        Request $request,
        bool $success,
        ?string $action,
        string $message,
        ?string $attendanceState = null,
        ?string $attendanceLabel = null
    ): RedirectResponse|JsonResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'action' => $action,
                'message' => $message,
                'attendance_state' => $attendanceState,
                'attendance_label' => $attendanceLabel,
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('dashboard_status', $message);
    }
}
