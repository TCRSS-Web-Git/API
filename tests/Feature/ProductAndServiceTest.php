<?php

namespace Tests\Feature;

use App\Enums\ProductAndServiceStatus;
use App\Models\ProductAndService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAndServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_product_and_services()
    {
        $this->signInSuperAdmin();

        [$product1, $product2, $product3, $product4] = ProductAndService::factory()->count(4)->create();

        // act
        $response = $this->getJson(route('product_and_services.index'));

        // assert
        $response->assertStatus(200);
        $response->assertSee($product1->hashid);
        $response->assertSee($product2->hashid);
        $response->assertSee($product3->hashid);
        $response->assertSee($product4->hashid);
    }

    public function test_get_all_product_and_services_with_filter_status()
    {
        $this->signInSuperAdmin();

        [$publishedProduct1, $publishedProduct2] = ProductAndService::factory()->published()->count(2)->create();
        [$draftProduct1, $draftProduct2] = ProductAndService::factory()->draft()->count(2)->create();

        // act
        $response = $this->getJson(route('product_and_services.index', ['status' => ProductAndServiceStatus::PUBLISHED->value]));

        // assert
        $response->assertStatus(200);
        $response->assertSee($publishedProduct1->hashid);
        $response->assertSee($publishedProduct2->hashid);
        $response->assertDontSee($draftProduct1->hashid);
        $response->assertDontSee($draftProduct2->hashid);

        // act
        $response = $this->getJson(route('product_and_services.index', ['status' => ProductAndServiceStatus::DRAFT->value]));

        // assert
        $response->assertStatus(200);
        $response->assertSee($draftProduct1->hashid);
        $response->assertSee($draftProduct2->hashid);
        $response->assertDontSee($publishedProduct1->hashid);
        $response->assertDontSee($publishedProduct2->hashid);
    }
}
