<?php

namespace Tests\Feature;

use App\Models\AwardImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AwardImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_can_get_all_award_images(): void
    {
        // set up
        $this->signInAdmin();
        $imageA = AwardImage::factory()->create(['order' => 2]);
        $imageB = AwardImage::factory()->create(['order' => 0]);
        $imageC = AwardImage::factory()->create(['order' => 1]);

        // act
        $response = $this->getJson(route('award-images.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $imageA->hashid]);
        $response->assertJsonFragment(['id' => $imageB->hashid]);
        $response->assertJsonFragment(['id' => $imageC->hashid]);
        $this->assertEquals($imageB->hashid, $response->json('data.0.id'));
        $this->assertEquals($imageC->hashid, $response->json('data.1.id'));
        $this->assertEquals($imageA->hashid, $response->json('data.2.id'));
    }

    public function test_the_admin_can_create_first_award_image(): void
    {
        // set up
        $this->signInAdmin();
        $imageFile = UploadedFile::fake()->image('image.jpg');

        // act
        $response = $this->postJson(route('award-images.store'), [
            'image' => $imageFile,
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseCount('award_images', 1);
        $awardImageid = AwardImage::decodeHash($response->json('data.id'));
        $this->assertDatabaseHas('award_images', [
            'id' => $awardImageid,
            'order' => 0,
        ]);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', [
            'model_id' => $awardImageid,
            'model_type' => AwardImage::class,
            'collection_name' => AwardImage::MEDIA_COLLECTION_IMAGE,
            'file_name' => 'image.jpg',
        ]);
    }

    public function test_the_admin_can_create_a_award_image(): void
    {
        // set up
        $this->signInAdmin();
        AwardImage::factory()->create(['order' => 0]);
        AwardImage::factory()->create(['order' => 1]);
        $imageFile = UploadedFile::fake()->image('image.jpg');

        // act
        $response = $this->postJson(route('award-images.store'), [
            'image' => $imageFile,
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseCount('award_images', 3); // existed 2 + created 1
        $awardImageid = AwardImage::decodeHash($response->json('data.id'));
        $this->assertDatabaseHas('award_images', [
            'id' => $awardImageid,
            'order' => 2,
        ]);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', [
            'model_id' => $awardImageid,
            'model_type' => AwardImage::class,
            'collection_name' => AwardImage::MEDIA_COLLECTION_IMAGE,
            'file_name' => 'image.jpg',
        ]);
    }

    public function test_the_admin_can_delete_a_award_image(): void
    {
        // set up
        $this->signInAdmin();
        $imageA = AwardImage::factory()->create();

        // act
        $response = $this->deleteJson(route('award-images.destroy', $imageA));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseCount('award_images', 0);
        $this->assertDatabaseMissing('award_images', ['id' => $imageA->id]);
    }

    public function test_the_admin_can_reorder_award_images(): void
    {
        // set up
        $this->signInAdmin();
        $awardImageA = AwardImage::factory()->create(['order' => 0]);
        $awardImageB = AwardImage::factory()->create(['order' => 1]);
        $awardImageC = AwardImage::factory()->create(['order' => 2]);
        $awardImageD = AwardImage::factory()->create(['order' => 3]);

        // act
        $orderIds = [$awardImageC->hashid, $awardImageB->hashid, $awardImageD->hashid, $awardImageA->hashid];
        $response = $this->patchJson(route('award-images.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('award_images', ['id' => $awardImageC->id, 'order' => 0]);
        $this->assertDatabaseHas('award_images', ['id' => $awardImageB->id, 'order' => 1]);
        $this->assertDatabaseHas('award_images', ['id' => $awardImageD->id, 'order' => 2]);
        $this->assertDatabaseHas('award_images', ['id' => $awardImageA->id, 'order' => 3]);

        // act
        $orderIds = [$awardImageD->hashid, $awardImageC->hashid, $awardImageB->hashid, $awardImageA->hashid];
        $response = $this->patchJson(route('award-images.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('award_images', ['id' => $awardImageD->id, 'order' => 0]);
        $this->assertDatabaseHas('award_images', ['id' => $awardImageC->id, 'order' => 1]);
        $this->assertDatabaseHas('award_images', ['id' => $awardImageB->id, 'order' => 2]);
        $this->assertDatabaseHas('award_images', ['id' => $awardImageA->id, 'order' => 3]);
    }
}
