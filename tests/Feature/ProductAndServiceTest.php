<?php

namespace Tests\Feature;

use App\Enums\ProductAndServiceStatus;
use App\Models\Media;
use App\Models\ProductAndService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductAndServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_can_get_all_product_and_services()
    {
        $this->signInAdmin();

        [$product1, $product2, $product3, $product4] = ProductAndService::factory()->count(4)->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])->create();

        // act
        $response = $this->getJson(route('products-and-services.index'));

        // assert
        $response->assertStatus(200);
        $response->assertSee($product1->hashid);
        $response->assertSee($product2->hashid);
        $response->assertSee($product3->hashid);
        $response->assertSee($product4->hashid);
        $this->assertEquals($product1->hashid, $response->json('data.0.id'));
        $this->assertEquals($product2->hashid, $response->json('data.1.id'));
        $this->assertEquals($product3->hashid, $response->json('data.2.id'));
        $this->assertEquals($product4->hashid, $response->json('data.3.id'));
    }

    public function test_the_admin_can_get_all_product_and_services_with_filter_status()
    {
        $this->signInAdmin();

        [$publishedProduct1, $publishedProduct2] = ProductAndService::factory()->published()->count(2)->create();
        [$draftProduct1, $draftProduct2] = ProductAndService::factory()->draft()->count(2)->create();

        // act
        $response = $this->getJson(route('products-and-services.index', ['status' => ProductAndServiceStatus::PUBLISHED->value]));

        // assert
        $response->assertStatus(200);
        $response->assertSee($publishedProduct1->hashid);
        $response->assertSee($publishedProduct2->hashid);
        $response->assertDontSee($draftProduct1->hashid);
        $response->assertDontSee($draftProduct2->hashid);

        // act
        $response = $this->getJson(route('products-and-services.index', ['status' => ProductAndServiceStatus::DRAFT->value]));

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
        $response = $this->getJson(route('products-and-services.show', $productAndService));

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
        $response = $this->getJson(route('products-and-services.show', ['products_and_service' => $productAndService, 'include' => 'translations']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $productAndService->hashid]);
        $response->assertJsonFragment(['title' => $productAndService->title]);
        $response->assertJsonFragment(['title' => $productAndService->getTranslation('title', 'th')]);
        $response->assertJsonFragment(['title' => $productAndService->getTranslation('title', 'en')]);
        $response->assertJsonStructure(['data' => ['translations' => ['th' => ['title']]]]);
        $response->assertJsonStructure(['data' => ['translations' => ['en' => ['title']]]]);
    }

    public function test_the_admin_can_create_first_published_product_and_service(): void
    {
        // set up
        $this->signInAdmin();
        [$cover, $file] = $this->setupImages();

        // act
        $response = $this->postJson(route('products-and-services.store'), [
            'published_at' => null,
            'th' => [
                'title' => 'ชื่อผลิตภัณห์',
            ],
            'en' => [
                'title' => 'Product And Services name',
            ],
            'cover' => [
                'id' => null,
                'path' => $cover['path'],
            ],
            'file' => [
                'id' => null,
                'path' => $file['path'],
            ],
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseHas('product_and_service_translations', ['locale' => 'th', 'title' => 'ชื่อผลิตภัณห์']);
        $this->assertDatabaseHas('product_and_service_translations', ['locale' => 'en', 'title' => 'Product And Services name']);
        $response->assertJsonFragment(['status' => ProductAndServiceStatus::DRAFT]);

        $productAndServiceId = ProductAndService::decodeHash($response->json('data.id'));
        $this->assertDatabaseCount('product_and_services', 1); // created 1
        $this->assertDatabaseHas('product_and_services', [
            'id' => $productAndServiceId,
            'order' => 0,
        ]);
        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'model_type' => ProductAndService::class,
            'model_id' => $productAndServiceId,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_COVER,
            'file_name' => $cover['name'],
        ]);
        $this->assertDatabaseHas('media', [
            'model_type' => ProductAndService::class,
            'model_id' => $productAndServiceId,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_FILE,
            'file_name' => $file['name'],
        ]);
    }

    public function test_the_admin_can_create_a_published_product_and_service(): void
    {
        // set up
        $this->signInAdmin();
        ProductAndService::factory()->create(['order' => 0]); // for test `order`
        ProductAndService::factory()->create(['order' => 1]); // for test `order`

        [$cover, $file] = $this->setupImages();

        // act
        $response = $this->postJson(route('products-and-services.store'), [
            'published_at' => now()->subDay(),
            'th' => [
                'title' => 'ชื่อผลิตภัณห์',
            ],
            'en' => [
                'title' => 'Product And Services name',
            ],
            'cover' => [
                'id' => null,
                'path' => $cover['path'],
            ],
            'file' => [
                'id' => null,
                'path' => $file['path'],
            ],
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseHas('product_and_service_translations', ['locale' => 'th', 'title' => 'ชื่อผลิตภัณห์']);
        $this->assertDatabaseHas('product_and_service_translations', ['locale' => 'en', 'title' => 'Product And Services name']);
        $response->assertJsonFragment(['status' => ProductAndServiceStatus::PUBLISHED]);

        $productAndServiceId = ProductAndService::decodeHash($response->json('data.id'));
        $this->assertDatabaseCount('product_and_services', 3); // existed 2 + created 1
        $this->assertDatabaseHas('product_and_services', [
            'id' => $productAndServiceId,
            'order' => 2,
        ]);
        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'model_type' => ProductAndService::class,
            'model_id' => $productAndServiceId,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_COVER,
            'file_name' => $cover['name'],
        ]);
        $this->assertDatabaseHas('media', [
            'model_type' => ProductAndService::class,
            'model_id' => $productAndServiceId,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_FILE,
            'file_name' => $file['name'],
        ]);
    }

    public function test_the_admin_cannot_create_a_duplicate_title_product_and_service(): void
    {
        // set up
        $this->signInAdmin();
        $existProductAndService = ProductAndService::factory()->create();
        $existProductAndService->setTranslation('title', 'ชื่อผลิตภัณห์', 'en');
        $existProductAndService->setTranslation('title', 'Product And Services name', 'th');
        $existProductAndService->save();

        // act
        $response = $this->postJson(route('products-and-services.store'), [
            'published_at' => null,
            'th' => [
                'title' => 'ชื่อผลิตภัณห์',
            ],
            'en' => [
                'title' => 'Product And Services name',
            ],
        ]);

        // assert
        $response->assertUnprocessable();
    }

    private function setupImages()
    {
        $fileCover = UploadedFile::fake()->image('image_cover.jpg');
        $file = UploadedFile::fake()->image('image_file.jpg');
        $mediaImageCover = $this->postJson(route('temporary_media.store'), ['media' => $fileCover]);
        $mediaImageThumbnail = $this->postJson(route('temporary_media.store'), ['media' => $file]);

        return [$mediaImageCover->json('data'), $mediaImageThumbnail->json('data')];
    }

    /**
     * Test the admin cannot create a published product_and_service when data is required.
     */
    public function test_the_admin_cannot_create_a_published_product_and_service_when_data_is_required(): void
    {
        // set up
        $this->signInAdmin();

        // act
        $response = $this->postJson(route('products-and-services.store'), [
            'published_at' => now()->subDay(),
        ]);

        // assert
        $response->assertUnprocessable();
        $this->assertDatabaseCount('product_and_services', 0);
    }

    public function test_the_admin_can_update_a_product_and_service_with_images(): void
    {
        // set up
        $this->signInAdmin();
        ProductAndService::factory()->count(2)->create(); // for test order
        $productAndService = ProductAndService::factory()->create(['order' => 0]);

        [$cover, $file] = $this->setupImages();

        $existedMediaA = Media::factory()->for(
            $productAndService,
            'model'
        )->create([
            'file_name' => 'existed_cover_file.png',
            'collection_name' => ProductAndService::MEDIA_COLLECTION_COVER,
        ]);

        // act
        $response = $this->putJson(route('products-and-services.update', $productAndService), [
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => 'ชื่อผลิตภัณห์',
            ],
            'en' => [
                'title' => 'Product And Services name',
            ],
            'cover' => [
                'id' => $existedMediaA->hashid,
            ],
            'file' => [
                'id' => null,
                'path' => $file['path'],
            ],
        ]);

        // assert
        $this->assertDatabaseHas('product_and_service_translations', ['item_id' => $productAndService->id, 'locale' => 'th', 'title' => 'ชื่อผลิตภัณห์']);
        $this->assertDatabaseHas('product_and_service_translations', ['item_id' => $productAndService->id, 'locale' => 'en', 'title' => 'Product And Services name']);
        $response->assertJsonFragment(['status' => ProductAndServiceStatus::DRAFT]);

        $this->assertDatabaseCount('product_and_services', 3); // existed 2 + created 1
        $this->assertDatabaseHas('product_and_services', [
            'id' => $productAndService->id,
            'order' => 0,
        ]);
        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'id' => $existedMediaA->id,
            'model_id' => $productAndService->id,
            'model_type' => ProductAndService::class,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_COVER,
            'file_name' => 'existed_cover_file.png',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $productAndService->id,
            'model_type' => ProductAndService::class,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_FILE,
            'file_name' => $file['name'],
        ]);
    }

    public function test_the_admin_can_update_a_product_and_service_with_old_title(): void
    {
        // set up
        $this->signInAdmin();
        ProductAndService::factory()->count(2)->create(); // for test order
        /** @var ProductAndService $productAndService */
        $productAndService = ProductAndService::factory()->create(['order' => 0]);

        [$cover, $file] = $this->setupImages();

        $existedMediaA = Media::factory()->for(
            $productAndService,
            'model'
        )->create([
            'file_name' => 'existed_cover_file.png',
            'collection_name' => ProductAndService::MEDIA_COLLECTION_COVER,
        ]);

        // act
        $response = $this->putJson(route('products-and-services.update', $productAndService), [
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => $productAndService->getTranslation('title', 'th'),
            ],
            'en' => [
                'title' => $productAndService->getTranslation('title', 'en'),
            ],
            'cover' => [
                'id' => $existedMediaA->hashid,
            ],
            'file' => [
                'id' => null,
                'path' => $file['path'],
            ],
        ]);

        // assert
        $this->assertDatabaseHas('product_and_service_translations', ['item_id' => $productAndService->id, 'locale' => 'th', 'title' => $productAndService->getTranslation('title', 'th')]);
        $this->assertDatabaseHas('product_and_service_translations', ['item_id' => $productAndService->id, 'locale' => 'en', 'title' => $productAndService->getTranslation('title', 'en')]);
        $response->assertJsonFragment(['status' => ProductAndServiceStatus::DRAFT]);

        $this->assertDatabaseCount('product_and_services', 3); // existed 2 + created 1
        $this->assertDatabaseHas('product_and_services', [
            'id' => $productAndService->id,
            'order' => 0,
        ]);
        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'id' => $existedMediaA->id,
            'model_id' => $productAndService->id,
            'model_type' => ProductAndService::class,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_COVER,
            'file_name' => 'existed_cover_file.png',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $productAndService->id,
            'model_type' => ProductAndService::class,
            'collection_name' => ProductAndService::MEDIA_COLLECTION_FILE,
            'file_name' => $file['name'],
        ]);
    }

    public function test_the_admin_can_delete_a_product_and_service(): void
    {
        // set up
        $this->signInAdmin();
        $productAndService = ProductAndService::factory()->create();

        // act
        $response = $this->deleteJson(route('products-and-services.destroy', $productAndService));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('product_and_services', ['id' => $productAndService->id]);
    }

    public function test_the_admin_can_reorder_product_and_service(): void
    {
        // set up
        $this->signInAdmin();
        $productAndServiceA = ProductAndService::factory()->create(['order' => 0]);
        $productAndServiceB = ProductAndService::factory()->create(['order' => 1]);
        $productAndServiceC = ProductAndService::factory()->create(['order' => 2]);
        $productAndServiceD = ProductAndService::factory()->create(['order' => 3]);

        // act
        $orderIds = [$productAndServiceC->hashid, $productAndServiceB->hashid, $productAndServiceD->hashid, $productAndServiceA->hashid];
        $response = $this->patchJson(route('products-and-services.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceC->id, 'order' => 0]);
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceB->id, 'order' => 1]);
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceD->id, 'order' => 2]);
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceA->id, 'order' => 3]);

        // act
        $orderIds = [$productAndServiceD->hashid, $productAndServiceC->hashid, $productAndServiceB->hashid, $productAndServiceA->hashid];
        $response = $this->patchJson(route('products-and-services.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceD->id, 'order' => 0]);
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceC->id, 'order' => 1]);
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceB->id, 'order' => 2]);
        $this->assertDatabaseHas('product_and_services', ['id' => $productAndServiceA->id, 'order' => 3]);
    }
}
