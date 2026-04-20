<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_menu(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $this->actingAs($owner)
            ->post(route('dashboard.menus.store'), [
                'name' => 'Kopi Susu',
                'category' => 'Coffee',
                'price' => 13000,
                'description' => 'Kopi susu dingin.',
                'image_path' => 'images/menu-gula-aren.jpg',
                'tags_input' => 'creamy,sweet',
                'sort_order' => 2,
                'is_active' => '1',
                'redirect_to' => 'dashboard.menus.index',
            ])
            ->assertRedirect(route('dashboard.menus.index'));

        $this->assertDatabaseHas('menus', [
            'name' => 'Kopi Susu',
            'category' => 'Coffee',
            'price' => 13000,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_owner_can_update_menu(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $menu = Menu::query()->create([
            'name' => 'Matcha',
            'category' => 'Non Coffee',
            'price' => 12000,
            'description' => 'Menu awal',
            'tags' => ['fresh'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->patch(route('dashboard.menus.update', $menu), [
                'name' => 'Matcha Latte',
                'category' => 'Signature',
                'price' => 15000,
                'description' => 'Menu update',
                'image_path' => 'images/menu-matcha.jpg',
                'tags_input' => 'fresh,milky',
                'sort_order' => 5,
                'redirect_to' => 'dashboard.menus.index',
            ])
            ->assertRedirect(route('dashboard.menus.index'));

        $updatedMenu = $menu->fresh();

        $this->assertSame('Matcha Latte', $updatedMenu?->name);
        $this->assertSame('Signature', $updatedMenu?->category);
        $this->assertSame(15000, $updatedMenu?->price);
        $this->assertFalse((bool) $updatedMenu?->is_active);
        $this->assertSame(['fresh', 'milky'], $updatedMenu?->tags);
    }

    public function test_owner_can_delete_menu(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $menu = Menu::query()->create([
            'name' => 'Taro',
            'category' => 'Non Coffee',
            'price' => 12000,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->delete(route('dashboard.menus.destroy', $menu), [
                'redirect_to' => 'dashboard.menus.index',
            ])
            ->assertRedirect(route('dashboard.menus.index'));

        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id,
        ]);
    }

    public function test_owner_can_upload_menu_image_from_file_input(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'admin@kopikeliling.com',
        ]);

        $this->actingAs($owner)
            ->post(route('dashboard.menus.store'), [
                'name' => 'Americano',
                'category' => 'Coffee',
                'price' => 10000,
                'description' => 'Kopi hitam.',
                'tags_input' => 'bold',
                'sort_order' => 1,
                'is_active' => '1',
                'redirect_to' => 'dashboard.menus.index',
                'image_file' => UploadedFile::fake()->create('americano.jpg', 128, 'image/jpeg'),
            ])
            ->assertRedirect(route('dashboard.menus.index'));

        $menu = Menu::query()->where('name', 'Americano')->firstOrFail();

        $this->assertNotNull($menu->image_path);
        $this->assertStringStartsWith('storage/menus/', (string) $menu->image_path);

        $storedPath = str_replace('storage/', '', (string) $menu->image_path);
        Storage::disk('public')->assertExists($storedPath);
    }
}
