<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends BaseDashboardController
{
    public function index(Request $request): View
    {
        $this->abortUnlessOwner($request->user());

        $menus = Menu::query()
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('dashboard.owner.menus.index', [
            'menus' => $menus,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->abortUnlessOwner($request->user());

        Menu::query()->create($this->extractMenuPayload($request));

        return $this->redirectWithDashboardStatus($request, 'Menu baru berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $this->abortUnlessOwner($request->user());

        $menu->update($this->extractMenuPayload($request, $menu));

        return $this->redirectWithDashboardStatus($request, 'Data menu berhasil diperbarui.');
    }

    public function destroy(Request $request, Menu $menu): RedirectResponse
    {
        $this->abortUnlessOwner($request->user());

        $this->deleteManagedMenuImage($menu->image_path);

        $menuName = $menu->name;
        $menu->delete();

        return $this->redirectWithDashboardStatus($request, "Menu {$menuName} berhasil dihapus.");
    }
}
