<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\Owner\StoreAssignmentRequest;
use App\Models\DriverUnitAssignment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends BaseDashboardController
{
    public function index(Request $request): View
    {
        $this->abortUnlessOwner($request->user());

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

        return view('dashboard.owner.assignments.index', [
            'drivers' => $drivers,
            'units' => $units,
            'activeAssignments' => $activeAssignments,
        ]);
    }

    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $owner = $request->user();
        $validated = $request->validated();

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

    public function finish(Request $request, DriverUnitAssignment $assignment): RedirectResponse
    {
        $this->abortUnlessOwner($request->user());

        if ($assignment->status === 'active' && $assignment->ended_at === null) {
            $assignment->update([
                'status' => 'completed',
                'ended_at' => now(),
                'checked_out_at' => $assignment->checked_out_at ?? now(),
            ]);
        }

        return $this->redirectWithDashboardStatus($request, 'Assignment driver berhasil diselesaikan.');
    }
}
