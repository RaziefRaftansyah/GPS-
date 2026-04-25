<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\Owner\StoreDriverRequest;
use App\Http\Requests\Dashboard\Owner\StoreUnitRequest;
use App\Http\Requests\Dashboard\Owner\UpdateDriverRequest;
use App\Http\Requests\Dashboard\Owner\UpdateUnitRequest;
use App\Models\DriverUnitAssignment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ResourceController extends BaseDashboardController
{
    public function index(Request $request): View
    {
        $this->abortUnlessOwner($request->user());

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

        return view('dashboard.owner.resources.index', [
            'drivers' => $drivers,
            'units' => $units,
        ]);
    }

    public function storeDriver(StoreDriverRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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

    public function updateDriver(UpdateDriverRequest $request, User $user): RedirectResponse
    {
        if (! $user->isDriver()) {
            return $this->redirectWithDashboardStatus($request, 'User yang dipilih bukan akun driver.');
        }

        $validated = $request->validated();

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
        $this->abortUnlessOwner($request->user());

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

    public function storeUnit(StoreUnitRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Unit::query()->create($validated);

        return $this->redirectWithDashboardStatus($request, 'Gerobak baru berhasil ditambahkan.');
    }

    public function updateUnit(UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validated();

        $unit->update($validated);

        return $this->redirectWithDashboardStatus($request, 'Data gerobak berhasil diperbarui.');
    }

    public function destroyUnit(Request $request, Unit $unit): RedirectResponse
    {
        $this->abortUnlessOwner($request->user());

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
}
