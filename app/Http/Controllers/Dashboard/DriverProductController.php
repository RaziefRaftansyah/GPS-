<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverProductController extends BaseDashboardController
{
    public function index(Request $request): View
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
            ->all();

        return view('dashboard.driver.products.index', [
            'driver' => $driver,
            'menus' => $menus,
            'selectedMenuIds' => $selectedMenuIds,
        ]);
    }

    public function update(Request $request): RedirectResponse
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
}
