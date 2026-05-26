<?php

namespace Tests\Feature;

use App\Models\PackagedProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackagedProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_packaged_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = PackagedProduct::create([
            'sku' => 'SP0001',
            'name' => 'Tra la nam',
            'description' => 'Mo ta cu',
            'category' => 'Tra thao moc',
            'unit' => 'goi',
            'stock_quantity' => 20,
            'expiry_date' => now()->addMonths(6)->toDateString(),
            'price' => 50000,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.packaged-products.update', $product), [
                'name' => 'Tra la nam 10 vi',
                'description' => 'Thanh phan moi',
                'category' => 'Khac',
                'unit' => 'hop',
                'stock_quantity' => 15,
                'expiry_date' => now()->addYear()->toDateString(),
                'status' => 'inactive',
            ])
            ->assertRedirect(route('admin.warehouse.index', ['tab' => 'products']));

        $this->assertDatabaseHas('packaged_products', [
            'id' => $product->id,
            'name' => 'Tra la nam 10 vi',
            'description' => 'Thanh phan moi',
            'category' => 'Khac',
            'unit' => 'hop',
            'stock_quantity' => 15,
            'price' => 50000,
            'status' => 'inactive',
        ]);
        $this->assertSame(now()->addYear()->toDateString(), $product->fresh()->expiry_date?->toDateString());
    }

    public function test_warehouse_product_edit_buttons_use_data_attributes_for_special_text(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        PackagedProduct::create([
            'sku' => 'SP0002',
            'name' => "Ruou thuoc xoa bop 'AmaTrung'",
            'description' => "Dong 1\nDong 2 co dau ' nhay",
            'category' => 'Khac',
            'unit' => 'hop',
            'stock_quantity' => 10,
            'expiry_date' => now()->addYear()->toDateString(),
            'price' => 100000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.warehouse.index', ['tab' => 'products']));

        $response->assertOk();
        $response->assertSee('class="btn-product-edit"', false);
        $response->assertSee('data-description=', false);
        $response->assertSee('data-expiry=', false);
        $response->assertSee('Hạn sử dụng');
        $response->assertSee('Còn hàng');
        $response->assertDontSee('Giá bán');
        $response->assertDontSee('onclick="openEditProductModal', false);
    }
}
