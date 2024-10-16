<?php

namespace Tests\Feature;

use App\Enums\ProductAndServiceStatus;
use App\Models\Media;
use App\Models\ProductAndService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductAndServiceTest extends TestCase
{
    use refreshDatabase;

    public function test_the_admin_can_create_a_published_product_and_service(): void
    {
        // set up
        $this->signInAdmin();

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

        $this->assertDatabaseCount('product_and_services', 1);
        $this->assertDatabaseCount('media', 2);
        $productAndServiceId = ProductAndService::decodeHash($response->json('data.id'));
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

    public function test_the_admin_can_update_a_Product_and_service_with_images(): void
    {
        // set up
        $this->signInAdmin();
        $productAndService = ProductAndService::factory()->create();

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
        $response->dump();
        $this->assertDatabaseHas('product_and_service_translations', ['item_id' => $productAndService->id, 'locale' => 'th', 'title' => 'ชื่อผลิตภัณห์']);
        $this->assertDatabaseHas('product_and_service_translations', ['item_id' => $productAndService->id, 'locale' => 'en', 'title' => 'Product And Services name']);
        $response->assertJsonFragment(['status' => ProductAndServiceStatus::DRAFT]);

        $this->assertDatabaseCount('product_and_services', 1);
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

    public function test_the_admin_can_delete_a_Product_and_service(): void
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
}
