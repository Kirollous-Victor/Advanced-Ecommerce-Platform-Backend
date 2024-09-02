<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class ProductTest extends TestCase
{
    use DatabaseTransactions;

    #[Test] public function a_product_can_be_created(): void
    {
        $response = $this->post('api/products', [
            'name' => 'New Product',
            'description' => 'New Product',
            'vendor_id' => 1,
            'category_id' => 1,
            'price' => 99.99,
            'stock' => 100
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
        ]);
    }
}
