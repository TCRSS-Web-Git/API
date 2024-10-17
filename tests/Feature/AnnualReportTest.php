<?php

namespace Tests\Feature;

use App\Enums\AnnualReportStatus;
use App\Models\AnnualReport;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AnnualReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_can_get_all_annual_reports()
    {
        $this->signInAdmin();

        [$annualReport1, $annualReport2, $annualReport3, $annualReport4] = AnnualReport::factory()->count(4)->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])->create();

        // act
        $response = $this->getJson(route('annual-reports.index'));

        // assert
        $response->assertStatus(200);
        $response->assertSee($annualReport1->hashid);
        $response->assertSee($annualReport2->hashid);
        $response->assertSee($annualReport3->hashid);
        $response->assertSee($annualReport4->hashid);
        $this->assertEquals($annualReport1->hashid, $response->json('data.0.id'));
        $this->assertEquals($annualReport2->hashid, $response->json('data.1.id'));
        $this->assertEquals($annualReport3->hashid, $response->json('data.2.id'));
        $this->assertEquals($annualReport4->hashid, $response->json('data.3.id'));
    }

    public function test_the_admin_can_get_all_annual_reports_with_filter_status()
    {
        $this->signInAdmin();

        [$publishedAnnualReport1, $publishedAnnualReport2] = AnnualReport::factory()->published()->count(2)->create();
        [$draftAnnualReport1, $draftAnnualReport2] = AnnualReport::factory()->draft()->count(2)->create();

        // act
        $response = $this->getJson(route('annual-reports.index', ['status' => AnnualReportStatus::PUBLISHED->value]));

        // assert
        $response->assertStatus(200);
        $response->assertSee($publishedAnnualReport1->hashid);
        $response->assertSee($publishedAnnualReport2->hashid);
        $response->assertDontSee($draftAnnualReport1->hashid);
        $response->assertDontSee($draftAnnualReport2->hashid);

        // act
        $response = $this->getJson(route('annual-reports.index', ['status' => AnnualReportStatus::DRAFT->value]));

        // assert
        $response->assertStatus(200);
        $response->assertSee($draftAnnualReport1->hashid);
        $response->assertSee($draftAnnualReport2->hashid);
        $response->assertDontSee($publishedAnnualReport1->hashid);
        $response->assertDontSee($publishedAnnualReport2->hashid);
    }

    public function test_the_admin_can_get_a_annual_report_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $annualReport = AnnualReport::factory()->create();
        $response = $this->getJson(route('annual-reports.show', $annualReport));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $annualReport->hashid]);
        $response->assertJsonFragment(['title' => $annualReport->title]);
    }

    public function test_the_admin_can_get_a_annual_report_by_id_with_all_translations(): void
    {
        // set up
        $this->signInAdmin();
        $annualReport = AnnualReport::factory()->create();

        // act
        $response = $this->getJson(route('annual-reports.show', ['annual_report' => $annualReport, 'include' => 'translations']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $annualReport->hashid]);
        $response->assertJsonFragment(['title' => $annualReport->title]);
        $response->assertJsonFragment(['title' => $annualReport->getTranslation('title', 'th')]);
        $response->assertJsonFragment(['title' => $annualReport->getTranslation('title', 'en')]);
        $response->assertJsonStructure(['data' => ['translations' => ['th' => ['title']]]]);
        $response->assertJsonStructure(['data' => ['translations' => ['en' => ['title']]]]);
    }

    public function test_the_admin_can_create_a_published_annual_report(): void
    {
        // set up
        $this->signInAdmin();
        AnnualReport::factory()->create(['order' => 0]); // for test `order`
        AnnualReport::factory()->create(['order' => 1]); // for test `order`

        [$cover, $file] = $this->setupImages();

        // act
        $response = $this->postJson(route('annual-reports.store'), [
            'published_at' => now()->subDay(),
            'th' => [
                'title' => 'ชื่อรายงานประจำปี',
            ],
            'en' => [
                'title' => 'Annual Report name',
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
        $this->assertDatabaseHas('annual_report_translations', ['locale' => 'th', 'title' => 'ชื่อรายงานประจำปี']);
        $this->assertDatabaseHas('annual_report_translations', ['locale' => 'en', 'title' => 'Annual Report name']);
        $response->assertJsonFragment(['status' => AnnualReportStatus::PUBLISHED]);

        $this->assertDatabaseCount('annual_reports', 3); // existed 2 + created 1
        $annualReportId = AnnualReport::decodeHash($response->json('data.id'));
        $this->assertDatabaseHas('annual_reports', [
            'id' => $annualReportId,
            'order' => 2,
        ]);
        $this->assertDatabaseCount('media', 2);

        $this->assertDatabaseHas('media', [
            'model_type' => AnnualReport::class,
            'model_id' => $annualReportId,
            'collection_name' => AnnualReport::MEDIA_COLLECTION_COVER,
            'file_name' => $cover['name'],
        ]);
        $this->assertDatabaseHas('media', [
            'model_type' => AnnualReport::class,
            'model_id' => $annualReportId,
            'collection_name' => AnnualReport::MEDIA_COLLECTION_FILE,
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

    public function test_the_admin_cannot_create_a_duplicate_title_annual_report(): void
    {
        // set up
        $this->signInAdmin();
        $existAnnualReport = AnnualReport::factory()->create();
        $existAnnualReport->setTranslation('title', 'ชื่อรายงานประจำปี', 'th');
        $existAnnualReport->setTranslation('title', 'Annual Report name', 'en');
        $existAnnualReport->save();

        // act
        $response = $this->postJson(route('annual-reports.store'), [
            'published_at' => null,
            'th' => [
                'title' => 'ชื่อรายงานประจำปี',
            ],
            'en' => [
                'title' => 'Annual Report name',
            ],
        ]);

        // assert
        $response->assertUnprocessable();
    }

    /**
     * Test the admin cannot create a published annual_report when data is required.
     */
    public function test_the_admin_cannot_create_a_published_annual_report_when_data_is_required(): void
    {
        // set up
        $this->signInAdmin();

        // act
        $response = $this->postJson(route('annual-reports.store'), [
            'published_at' => now()->subDay(),
        ]);

        // assert
        $response->assertUnprocessable();
        $this->assertDatabaseCount('annual_reports', 0);
    }

    public function test_the_admin_can_update_a_annual_report_with_images(): void
    {
        // set up
        $this->signInAdmin();
        AnnualReport::factory()->count(2)->create(); // for test order
        $annualReport = AnnualReport::factory()->create(['order' => 0]);

        [$cover, $file] = $this->setupImages();

        $existedMediaA = Media::factory()->for(
            $annualReport,
            'model'
        )->create([
            'file_name' => 'existed_cover_file.png',
            'collection_name' => AnnualReport::MEDIA_COLLECTION_COVER,
        ]);

        // act
        $response = $this->putJson(route('annual-reports.update', $annualReport), [
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => 'ชื่อรายงานประจำปี',
            ],
            'en' => [
                'title' => 'Annual Report name',
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
        $this->assertDatabaseHas('annual_report_translations', ['item_id' => $annualReport->id, 'locale' => 'th', 'title' => 'ชื่อรายงานประจำปี']);
        $this->assertDatabaseHas('annual_report_translations', ['item_id' => $annualReport->id, 'locale' => 'en', 'title' => 'Annual Report name']);
        $response->assertJsonFragment(['status' => AnnualReportStatus::DRAFT]);

        $this->assertDatabaseCount('annual_reports', 3); // existed 2 + created 1
        $this->assertDatabaseHas('annual_reports', [
            'id' => $annualReport->id,
            'order' => 0,
        ]);
        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'id' => $existedMediaA->id,
            'model_id' => $annualReport->id,
            'model_type' => AnnualReport::class,
            'collection_name' => AnnualReport::MEDIA_COLLECTION_COVER,
            'file_name' => 'existed_cover_file.png',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $annualReport->id,
            'model_type' => AnnualReport::class,
            'collection_name' => AnnualReport::MEDIA_COLLECTION_FILE,
            'file_name' => $file['name'],
        ]);
    }

    public function test_the_admin_can_update_a_annual_report_with_old_title(): void
    {
        // set up
        $this->signInAdmin();
        AnnualReport::factory()->count(2)->create(); // for test order
        /** @var AnnualReport $annualReport */
        $annualReport = AnnualReport::factory()->create(['order' => 0]);

        [$cover, $file] = $this->setupImages();

        $existedMediaA = Media::factory()->for(
            $annualReport,
            'model'
        )->create([
            'file_name' => 'existed_cover_file.png',
            'collection_name' => AnnualReport::MEDIA_COLLECTION_COVER,
        ]);

        // act
        $response = $this->putJson(route('annual-reports.update', $annualReport), [
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => $annualReport->getTranslation('title', 'th'),
            ],
            'en' => [
                'title' => $annualReport->getTranslation('title', 'en'),
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
        $this->assertDatabaseHas('annual_report_translations', ['item_id' => $annualReport->id, 'locale' => 'th', 'title' => $annualReport->getTranslation('title', 'th')]);
        $this->assertDatabaseHas('annual_report_translations', ['item_id' => $annualReport->id, 'locale' => 'en', 'title' => $annualReport->getTranslation('title', 'en')]);
        $response->assertJsonFragment(['status' => AnnualReportStatus::DRAFT]);

        $this->assertDatabaseCount('annual_reports', 3); // existed 2 + created 1
        $this->assertDatabaseHas('annual_reports', [
            'id' => $annualReport->id,
            'order' => 0,
        ]);
        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'id' => $existedMediaA->id,
            'model_id' => $annualReport->id,
            'model_type' => AnnualReport::class,
            'collection_name' => AnnualReport::MEDIA_COLLECTION_COVER,
            'file_name' => 'existed_cover_file.png',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $annualReport->id,
            'model_type' => AnnualReport::class,
            'collection_name' => AnnualReport::MEDIA_COLLECTION_FILE,
            'file_name' => $file['name'],
        ]);
    }

    public function test_the_admin_can_delete_a_annual_report(): void
    {
        // set up
        $this->signInAdmin();
        $annualReport = AnnualReport::factory()->create();

        // act
        $response = $this->deleteJson(route('annual-reports.destroy', $annualReport));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('annual_reports', ['id' => $annualReport->id]);
    }

    public function test_the_admin_can_reorder_annual_report(): void
    {
        // set up
        $this->signInAdmin();
        $annualReportA = AnnualReport::factory()->create(['order' => 0]);
        $annualReportB = AnnualReport::factory()->create(['order' => 1]);
        $annualReportC = AnnualReport::factory()->create(['order' => 2]);
        $annualReportD = AnnualReport::factory()->create(['order' => 3]);

        // act
        $orderIds = [$annualReportC->hashid, $annualReportB->hashid, $annualReportD->hashid, $annualReportA->hashid];
        $response = $this->patchJson(route('annual-reports.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportC->id, 'order' => 0]);
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportB->id, 'order' => 1]);
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportD->id, 'order' => 2]);
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportA->id, 'order' => 3]);

        // act
        $orderIds = [$annualReportD->hashid, $annualReportC->hashid, $annualReportB->hashid, $annualReportA->hashid];
        $response = $this->patchJson(route('annual-reports.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportD->id, 'order' => 0]);
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportC->id, 'order' => 1]);
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportB->id, 'order' => 2]);
        $this->assertDatabaseHas('annual_reports', ['id' => $annualReportA->id, 'order' => 3]);
    }
}
