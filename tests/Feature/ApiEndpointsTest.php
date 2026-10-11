<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_seeder_credentials_can_login_via_api(): void
    {
        $this->seed(\Database\Seeders\AdminSeeder::class);

        $response = $this->postJson('/api/v1/login', [
            'email'    => 'admin@admin.com',
            'password' => 'Admin@12345',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'admin@admin.com')
            ->assertJsonStructure([
                'data' => ['token', 'user'],
            ]);
    }

    public function test_public_product_routes_without_auth(): void
    {
        $product = Product::create([
            'name'        => 'Public T-Shirt ' . uniqid(),
            'description' => 'Cotton shirt description',
            'is_active'   => true,
        ]);

        // 1. List
        $this->getJson('/api/v1/products')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['name' => $product->name]);

        // 2. Detail
        $this->getJson('/api/v1/products/' . $product->id)
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', $product->name);
    }

    public function test_color_and_size_endpoints(): void
    {
        $user = User::factory()->create();

        $uniqueColorName = 'Emerald_' . uniqid();
        $uniqueSizeName = 'Size_' . uniqid();

        // Color create (auth)
        $colorRes = $this->actingAs($user, 'sanctum')->postJson('/api/v1/colors', [
            'color_name'     => $uniqueColorName,
            'color_hex_code' => '#50C878',
        ]);
        $colorRes->assertStatus(201);
        $colorId = $colorRes->json('data.id');

        // Color list (public)
        $this->getJson('/api/v1/colors')
            ->assertStatus(200)
            ->assertJsonFragment(['name' => $uniqueColorName]);

        // Size create (auth)
        $sizeRes = $this->actingAs($user, 'sanctum')->postJson('/api/v1/sizes', [
            'size_name' => $uniqueSizeName,
        ]);
        $sizeRes->assertStatus(201);
        $sizeId = $sizeRes->json('data.id');

        // Size list (public)
        $this->getJson('/api/v1/sizes')
            ->assertStatus(200)
            ->assertJsonFragment(['name' => $uniqueSizeName]);

        // Delete (auth)
        $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/colors/' . $colorId)->assertStatus(200);
        $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/sizes/' . $sizeId)->assertStatus(200);
    }

    public function test_product_crud_management(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $color = Color::create(['name' => 'Navy_' . uniqid(), 'hex_code' => '#000080']);
        $size = Size::create(['name' => 'Large_' . uniqid()]);

        $image = UploadedFile::fake()->create('shirt.jpg', 100, 'image/jpeg');

        // Create product (auth)
        $storeResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/products', [
            'name'        => 'Management Shirt ' . uniqid(),
            'description' => 'Shirt description',
            'is_active'   => true,
            'color_ids'   => [$color->id],
            'size_ids'    => [$size->id],
            'images'      => [$image],
            'variants'    => [
                [
                    'color_id' => $color->id,
                    'size_id'  => $size->id,
                    'sku'      => 'SKU-' . uniqid(),
                    'price'    => 45.00,
                    'stock'    => 20,
                ],
            ],
        ]);

        $storeResponse->assertStatus(201)
            ->assertJsonPath('success', true);
        $productId = $storeResponse->json('data.id');

        // Update product (auth)
        $updateResponse = $this->actingAs($user, 'sanctum')->putJson('/api/v1/products/' . $productId, [
            'name'        => 'Updated Management Shirt ' . uniqid(),
            'description' => 'Updated shirt description',
            'is_active'   => true,
            'variants'    => [
                [
                    'price' => 50.00,
                    'stock' => 15,
                ],
            ],
        ]);
        $updateResponse->assertStatus(200);

        // Delete product (auth)
        $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/products/' . $productId)->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $productId]);
    }
}
