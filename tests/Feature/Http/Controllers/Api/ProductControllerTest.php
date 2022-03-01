<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use WithFaker , RefreshDatabase;
    /** @test  */
    public function can_create_a_product()
    {
        $this->withoutExceptionHandling();
//        $res = $this->json('POST' , '/api/products',[
//            'name'=> $name = $this->faker->name,
//            'slug'=> Str::slug($name),
//            'price'=> $price=$this->faker->randomDigit()
//        ]);
        $res = $this->postJson('/api/products' , [
            'name'=> $name = $this->faker->name,
            'slug'=> Str::slug($name),
            'price'=> $price=$this->faker->randomDigit()
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
                'name'=>$name,
                'slug'=>Str::slug($name),
                'price'=>$price
            ])
            ->assertStatus(201);
        $this->assertDatabaseHas('products' , [
            'name'=>$name,
            'slug'=>Str::slug($name),
            'price'=>$price
        ]);
    }

    /** @test  */
    public function can_return_a_product()
    {
        $this->withoutExceptionHandling();
        //Given
        $product = Product::factory()->create();
        //when
        $res = $this->getJson('/api/product/'.$product->id);

        $res->assertJsonStructure([
            'id',
            'name',
            'slug',
            'price'
        ]);

        //then
        $res->assertExactJson([
            'id'=>$product->id,
            'name'=>$product->name,
            'slug'=>$product->slug,
            'price'=>$product->price,
            'created_at'=>$product->created_at,
        ])
        ->assertStatus(200);
    }

    /** @test  */
    public function will_fail_with_a_404_if_is_product_not_found()
    {
        $res = $this->getJson('/api/product/1000');

        $res->assertStatus(404);
    }

    /** @test  */
    public function will_fail_update_with_a_404_if_is_product_not_found()
    {
        $res = $this->putJson('/api/product/1000');

        $res->assertStatus(404);
    }

    /** @test  */
    public function can_update_product()
    {
        $this->withoutExceptionHandling();
        $product = Product::factory()->create();

        $res = $this->putJson("/api/product/$product->id" , [
            'name'=>$product->name.'-'."updated",
            'slug'=>Str::slug($product->name.'-'."updated"),
            'price'=>$product->price
        ]);
        $res->assertStatus(200)
        ->assertExactJson([
            'id'=>$product->id,
            'name'=>$product->name.'-'."updated",
            'slug'=>Str::slug($product->name.'-'."updated"),
            'price'=>$product->price,
            'created_at'=>$product->created_at,
        ]);

        $this->assertDatabaseHas('products' , [
            'id'=>$product->id,
            'name'=>$product->name.'-'."updated",
            'slug'=>Str::slug($product->name.'-'."updated"),
            'price'=>$product->price,
            'created_at'=>$product->created_at,
            'updated_at'=>$product->updated_at,
        ]);
    }

    /** @test  */
    public function will_fail_with_a_404_if_is_product_delete_not_found()
    {
        $res = $this->deleteJson('/api/product/1000');

        $res->assertStatus(404);
    }

    public function can_delete_product()
    {

    }
}
