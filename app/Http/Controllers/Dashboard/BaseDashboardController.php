<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DriverAttendanceLog;
use App\Models\DriverUnitAssignment;
use App\Models\Location;
use App\Models\Menu;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

abstract class BaseDashboardController extends Controller
{
    protected function adminEmail(): string
    {
        return (string) env('ADMIN_EMAIL', 'admin@kopikeliling.com');
    }

    protected function isOwner(?User $user): bool
    {
        return $user !== null
            && ($user->isOwner() || $user->email === $this->adminEmail());
    }

    protected function abortUnlessOwner(?User $user): void
    {
        if (! $this->isOwner($user)) {
            abort(403);
        }
    }

    protected function buildDriverDashboardView(User $driver): View
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

        return view('dashboard.driver.index', [
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

    protected function transformUnitForDashboard(Unit $unit): array
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

    protected function redirectWithDashboardStatus(Request $request, string $message): RedirectResponse
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

    protected function extractMenuPayload(Request $request, array $validated, ?Menu $existingMenu = null): array
    {
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

    protected function deleteManagedMenuImage(?string $imagePath): void
    {
        if (blank($imagePath) || ! Str::startsWith((string) $imagePath, 'storage/menus/')) {
            return;
        }

        $relativeStoragePath = Str::after((string) $imagePath, 'storage/');
        Storage::disk('public')->delete($relativeStoragePath);
    }

    protected function logDriverClockIn(User $driver, ?DriverUnitAssignment $assignment): void
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

    protected function logDriverClockOut(User $driver, ?DriverUnitAssignment $assignment): void
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

    protected function attendanceResponse(
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
