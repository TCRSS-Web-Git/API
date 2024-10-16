<?php

namespace Tests\Feature;

use App\Enums\ProductAndServiceStatus;
use App\Models\ProductAndService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAndServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_can_get_all_product_and_services()
    {
        $this->signInAdmin();

        [$product1, $product2, $product3, $product4] = ProductAndService::factory()->count(4)->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])->create();

        // act
        $response = $this->getJson(route('product_and_services.index'));

        // assert
        $response->assertStatus(200);
        $response->assertSee($product1->hashid);
        $response->assertSee($product2->hashid);
        $response->assertSee($product3->hashid);
        $response->assertSee($product4->hashid);
        $this->assertEquals($product4->hashid, $response->json('data.0.id'));
        $this->assertEquals($product3->hashid, $response->json('data.1.id'));
        $this->assertEquals($product2->hashid, $response->json('data.2.id'));
        $this->assertEquals($product1->hashid, $response->json('data.3.id'));
    }

    public function test_the_admin_can_get_all_product_and_services_with_filter_status()
    {
        $this->signInAdmin();

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

    public function test_the_admin_can_get_a_product_and_service_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $productAndService = ProductAndService::factory()->create();

        // act
        $response = $this->getJson(route('product_and_services.show', $productAndService));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $productAndService->hashid]);
        $response->assertJsonFragment(['title' => $productAndService->title]);
    }

    public function test_the_admin_can_get_a_product_and_service_by_id_with_all_translations(): void
    {
        // set up
        $this->signInAdmin();
        $productAndService = ProductAndService::factory()->create();

        // act
        $response = $this->getJson(route('product_and_services.show', ['product_and_service' => $productAndService, 'include' => 'translations']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $productAndService->hashid]);
        $response->assertJsonFragment(['title' => $productAndService->title]);
        $response->assertJsonFragment(['title' => $productAndService->getTranslation('title', 'th')]);
        $response->assertJsonFragment(['title' => $productAndService->getTranslation('title', 'en')]);
        $response->assertJsonStructure(['data' => ['translations' => ['th' => ['title']]]]);
        $response->assertJsonStructure(['data' => ['translations' => ['en' => ['title']]]]);
    }
}
