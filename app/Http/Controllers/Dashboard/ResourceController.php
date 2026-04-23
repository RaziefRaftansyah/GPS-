<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\DriverUnitAssignment;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    public function storeDriver(Request $request): RedirectResponse
    {
        $owner = $request->user();
        $this->abortUnlessOwner($owner);

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
        $this->abortUnlessOwner($request->user());

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

    public function storeUnit(Request $request): RedirectResponse
    {
        $this->abortUnlessOwner($request->user());

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
        $this->abortUnlessOwner($request->user());

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
