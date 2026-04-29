<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\DriverUnitAssignment;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends BaseDashboardController
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->isDriver()) {
            return $this->buildDriverDashboardView($user);
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

        $driverAttendanceQrPath = URL::signedRoute('dashboard.driver.attendance.qr', [], null, false);
        $publicAppUrl = rtrim((string) config('app.url'), '/');
        $driverAttendanceQrUrl = ($publicAppUrl !== '' ? $publicAppUrl : url('/')).$driverAttendanceQrPath;

        return view('dashboard.owner.index', [
            'activeUsers' => $activeUsers,
            'activeUserCount' => $activeUsers->count(),
            'activeSessionCount' => $activeSessions->count(),
            'latestLoginAt' => $activeUsers->first()['last_seen'] ?? null,
            'adminEmail' => $this->adminEmail(),
            'traccarEndpoint' => url('/api/location'),
            'traccarEndpointDisplay' => '/api/location',
            'units' => $units,
            'unitCount' => $units->count(),
            'assignedUnitCount' => $units->where('active_assignment', '!=', null)->count(),
            'drivers' => $drivers,
            'driverCount' => $drivers->count(),
            'availableDrivers' => $drivers->filter(fn (User $driver) => $driver->activeDriverAssignment === null)->values(),
            'activeAssignments' => $activeAssignments,
            'driverAttendanceQrLink' => $driverAttendanceQrUrl,
            'driverAttendanceQrDisplay' => $driverAttendanceQrUrl,
        ]);
    }

    public function kickUser(Request $request, User $user): RedirectResponse
    {
        $this->abortUnlessOwner($request->user());

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
}
