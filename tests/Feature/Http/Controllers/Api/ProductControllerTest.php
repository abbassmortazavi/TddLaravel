<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function can_paginate_product()
    {
        $products = Product::factory()->count(50)->create();

        $res = $this->actingAs(User::factory()->create())->getJson('/api/products');
        $res->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'price']
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'from', 'to', 'path', 'per_page', 'total']
            ]);

        // Log::info(1 , [$res->content()]);
    }

    /** @test */
    public function can_create_a_product()
    {
        $this->withoutExceptionHandling();
//        $res = $this->json('POST' , '/api/products',[
//            'name'=> $name = $this->faker->name,
//            'slug'=> Str::slug($name),
//            'price'=> $price=$this->faker->randomDigit()
//        ]);
        $res = $this->actingAs(User::factory()->create())->postJson('/api/products', [
            'name' => $name = $this->faker->name,
            'slug' => Str::slug($name),
            'price' => $price = $this->faker->randomDigit()
        ]);
        // Log::info(1 , [$res->content()]);

        $res->assertJsonStructure([
            'id',
            'name',
            'slug',
            'price',
            'created_at',
            'updated',
        ])
            ->assertJson([
                'name' => $name,
                'slug' => Str::slug($name),
                'price' => $price
            ])
            ->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => $price
        ]);
    }

    /** @test */
    public function can_return_a_product()
    {
        $this->withoutExceptionHandling();
        //Given
        $product = Product::factory()->create();
        //when
        $res = $this->actingAs(User::factory()->create())->getJson('/api/product/' . $product->id);

        $res->assertJsonStructure([
            'id',
            'name',
            'slug',
            'price'
        ]);

        //then
        $res->assertExactJson([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'created_at' => $product->created_at,
        ])
            ->assertStatus(200);
    }

    /** @test */
    public function will_fail_with_a_404_if_is_product_not_found()
    {
        $res = $this->actingAs(User::factory()->create())->getJson('/api/product/1000');

        $res->assertStatus(404);
    }

    /** @test */
    public function will_fail_update_with_a_404_if_is_product_not_found()
    {
        $res = $this->actingAs(User::factory()->create())->putJson('/api/product/1000');

        $res->assertStatus(404);
    }

    /** @test */
    public function can_update_product()
    {
        $this->withoutExceptionHandling();
        $product = Product::factory()->create();

        $res = $this->actingAs(User::factory()->create())->putJson("/api/product/$product->id", [
            'name' => $product->name . '-' . "updated",
            'slug' => Str::slug($product->name . '-' . "updated"),
            'price' => $product->price
        ]);
        $res->assertStatus(200)
            ->assertExactJson([
                'id' => $product->id,
                'name' => $product->name . '-' . "updated",
                'slug' => Str::slug($product->name . '-' . "updated"),
                'price' => $product->price,
                'created_at' => $product->created_at,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name . '-' . "updated",
            'slug' => Str::slug($product->name . '-' . "updated"),
            'price' => $product->price,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ]);
    }

    /** @test */
    public function will_fail_with_a_404_if_is_product_delete_not_found()
    {
        $res = $this->actingAs(User::factory()->create())->deleteJson('/api/product/1000');

        $res->assertStatus(404);
    }

    /** @test */
    public function can_delete_product()
    {
        $this->withoutExceptionHandling();
        $product = Product::factory()->create();
        $res = $this->actingAs(User::factory()->create())->deleteJson("/api/product/$product->id");

        $res->assertStatus(200)
            ->assertSee(null);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    /** @test  */
    public function none_authenticated_users_cannot_access_following_all_endpoints_for_products()
    {
        $index = $this->getJson('/api/products');
        $index->assertStatus(401);
    }
}
