<?php

namespace Tests\Feature;

use App\Enums\AwardStatus;
use App\Models\Award;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AwardTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_can_get_all_awards(): void
    {
        // set up
        $this->signInAdmin();
        $awardA = Award::factory()->create(['order' => 2]);
        $awardB = Award::factory()->create(['order' => 0]);
        $awardC = Award::factory()->create(['order' => 1]);

        // act
        $response = $this->getJson(route('awards.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $awardA->hashid]);
        $response->assertJsonFragment(['id' => $awardB->hashid]);
        $response->assertJsonFragment(['id' => $awardC->hashid]);
        $this->assertEquals($awardB->hashid, $response->json('data.0.id'));
        $this->assertEquals($awardC->hashid, $response->json('data.1.id'));
        $this->assertEquals($awardA->hashid, $response->json('data.2.id'));
    }

    public function test_the_admin_can_get_all_awards_with_filter_status(): void
    {
        // set up
        $this->signInAdmin();
        $awardA = Award::factory()->draft()->create();
        $awardB = Award::factory()->published()->create();
        $awardC = Award::factory()->published()->create();

        // act
        $response = $this->getJson(route('awards.index', ['status' => AwardStatus::DRAFT->value]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $awardA->hashid]);
        $response->assertJsonMissing(['id' => $awardB->hashid]);
        $response->assertJsonMissing(['id' => $awardC->hashid]);

        // act
        $response = $this->getJson(route('awards.index', ['status' => AwardStatus::PUBLISHED->value]));

        // assert
        $response->assertOk();
        $response->assertJsonMissing(['id' => $awardA->hashid]);
        $response->assertJsonFragment(['id' => $awardB->hashid]);
        $response->assertJsonFragment(['id' => $awardC->hashid]);
    }

    public function test_the_admin_can_get_an_award_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $award = Award::factory()->create();

        // act
        $response = $this->getJson(route('awards.show', $award));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $award->hashid, 'title' => $award->getTranslation('title'), 'description' => $award->getTranslation('description')]);
    }

    public function test_the_admin_can_get_an_award_by_id_with_translations(): void
    {
        // set up
        $this->signInAdmin();
        $award = Award::factory()->create();

        // act
        $response = $this->getJson(route('awards.show', ['award' => $award, 'include' => 'translations']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $award->hashid, 'title' => $award->getTranslation('title')]);
        $response->assertJsonFragment(['title' => $award->getTranslation('title', 'th')]);
        $response->assertJsonFragment(['title' => $award->getTranslation('title', 'en')]);
        $response->assertJsonStructure(['data' => ['translations' => ['th' => ['title']]]]);
        $response->assertJsonStructure(['data' => ['translations' => ['en' => ['title']]]]);
    }

    public function test_the_admin_can_get_an_award_by_id_localized(): void
    {
        // set up
        $this->signInAdmin();
        $award = Award::factory()->create();

        // act Thai
        $response = $this->getJson(route('awards.show', $award), ['X-Localization' => 'th']);

        // assert Thai
        $response->assertOk();
        $response->assertJsonFragment(['id' => $award->hashid]);
        $response->assertJsonFragment(['title' => $award->getTranslation('title', 'th')]);

        // act English
        $response = $this->getJson(route('awards.show', $award), ['X-Localization' => 'en']);

        // assert English
        $response->assertOk();
        $response->assertJsonFragment(['id' => $award->hashid]);
        $response->assertJsonFragment(['title' => $award->getTranslation('title', 'en')]);
    }

    public function test_the_admin_can_delete_an_award_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $award = Award::factory()->create();

        // act
        $response = $this->deleteJson(route('awards.destroy', $award));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseCount('awards', 0);
        $this->assertDatabaseMissing('awards', ['id' => $award->id]);
    }

    public function test_the_admin_can_reorder_awards(): void
    {
        // set up
        $this->signInAdmin();
        $awardA = Award::factory()->create(['order' => 0]);
        $awardB = Award::factory()->create(['order' => 1]);
        $awardC = Award::factory()->create(['order' => 2]);
        $awardD = Award::factory()->create(['order' => 3]);

        // act
        $orderIds = [$awardC->hashid, $awardB->hashid, $awardD->hashid, $awardA->hashid];
        $response = $this->patchJson(route('awards.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('awards', ['id' => $awardC->id, 'order' => 0]);
        $this->assertDatabaseHas('awards', ['id' => $awardB->id, 'order' => 1]);
        $this->assertDatabaseHas('awards', ['id' => $awardD->id, 'order' => 2]);
        $this->assertDatabaseHas('awards', ['id' => $awardA->id, 'order' => 3]);

        // act
        $orderIds = [$awardD->hashid, $awardC->hashid, $awardB->hashid, $awardA->hashid];
        $response = $this->patchJson(route('awards.reorder'), ['ids' => $orderIds]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('awards', ['id' => $awardD->id, 'order' => 0]);
        $this->assertDatabaseHas('awards', ['id' => $awardC->id, 'order' => 1]);
        $this->assertDatabaseHas('awards', ['id' => $awardB->id, 'order' => 2]);
        $this->assertDatabaseHas('awards', ['id' => $awardA->id, 'order' => 3]);
    }

    public function test_the_admin_can_create_a_published_award(): void
    {
        // set up
        $this->signInAdmin();
        $award = Award::factory()->published()->make()->toArray();

        // act
        $response = $this->postJson(route('awards.store'), [
            'published_at' => $award['published_at'],
            'th' => [
                'title' => 'ชื่อบทความ',
                'description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'description' => 'Blog content',
            ],
        ]);

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => AwardStatus::PUBLISHED, 'order' => 0]);
        $this->assertDatabaseCount('awards', 1);
        $awardId = Award::decodeHash($response->json('data.id'));
        $this->assertDatabaseHas('awards', [
            'id' => $awardId,
            'order' => 0,
            'published_at' => Carbon::parse($award['published_at']),
        ]);
        $this->assertDatabaseHas('award_translations', ['locale' => 'th', 'title' => 'ชื่อบทความ', 'description' => 'เนื้อหาบทความ']);
        $this->assertDatabaseHas('award_translations', ['locale' => 'en', 'title' => 'Blog name', 'description' => 'Blog content']);
    }

    public function test_the_admin_can_create_a_draft_award(): void
    {
        // set up
        $this->signInAdmin();
        Award::factory()->draft()->create(['order' => 0]); // for test order
        Award::factory()->draft()->create(['order' => 1]); // for test order
        $award = Award::factory()->draft()->make()->toArray();

        // act
        $response = $this->postJson(route('awards.store'), [
            'published_at' => $award['published_at'],
            'th' => [
                'title' => 'ชื่อบทความ',
                'description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'description' => 'Blog content',
            ],
        ]);

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => AwardStatus::DRAFT, 'order' => 2]);
        $this->assertDatabaseCount('awards', 3); // existed 2 + created 1
        $awardId = Award::decodeHash($response->json('data.id'));
        $this->assertDatabaseHas('awards', [
            'id' => $awardId,
            'order' => 2,
            'published_at' => $award['published_at'] ? Carbon::parse($award['published_at']) : null,
        ]);
        $this->assertDatabaseHas('award_translations', ['locale' => 'th', 'title' => 'ชื่อบทความ', 'description' => 'เนื้อหาบทความ']);
        $this->assertDatabaseHas('award_translations', ['locale' => 'en', 'title' => 'Blog name', 'description' => 'Blog content']);
    }

    public function test_the_admin_cannot_create_a_published_award(): void
    {
        // set up
        $this->signInAdmin();
        $award = Award::factory()->make()->toArray();

        // act
        $response = $this->postJson(route('awards.store'), [
            'published_at' => now()->subDay(),
        ]);

        // assert
        $response->assertUnprocessable();
    }

    public function test_the_admin_cannot_create_a_duplicate_title_award(): void
    {
        // set up
        $this->signInAdmin();
        $existAward = Award::factory()->create();
        $existAward->setTranslation('title', 'ชื่อรางวัล', 'th');
        $existAward->setTranslation('title', 'Award name', 'en');
        $existAward->save();

        // act
        $response = $this->postJson(route('awards.store'), [
            'published_at' => null,
            'th' => [
                'title' => 'ชื่อรางวัล',
            ],
            'en' => [
                'title' => 'Award name',
            ],
        ]);

        // assert
        $response->assertUnprocessable();
    }

    public function test_the_admin_can_update_an_award(): void
    {
        // set up
        $this->signInAdmin();
        Award::factory()->draft()->create(['order' => 0]); // for test order
        Award::factory()->draft()->create(['order' => 1]); // for test order
        $award = Award::factory()->draft()->create(['order' => 2]);

        // act
        $response = $this->putJson(route('awards.update', $award), [
            'published_at' => $publishedAt = Carbon::now()->subDay(),
            'th' => [
                'title' => 'ชื่อบทความ 2',
                'description' => 'เนื้อหาบทความ 2',
            ],
            'en' => [
                'title' => 'Blog name 2',
                'description' => 'Blog content 2',
            ],
        ]);

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => AwardStatus::PUBLISHED, 'order' => 2]);
        $this->assertDatabaseCount('awards', 3); // existed 2 + created 1
        $awardId = Award::decodeHash($response->json('data.id'));
        $this->assertDatabaseHas('awards', [
            'id' => $awardId,
            'order' => 2,
            'published_at' => $publishedAt,
        ]);
        $this->assertDatabaseHas('award_translations', ['locale' => 'th', 'title' => 'ชื่อบทความ 2', 'description' => 'เนื้อหาบทความ 2']);
        $this->assertDatabaseHas('award_translations', ['locale' => 'en', 'title' => 'Blog name 2', 'description' => 'Blog content 2']);
    }

    public function test_the_admin_can_update_an_award_with_old_title(): void
    {
        // set up
        $this->signInAdmin();
        Award::factory()->count(2)->create(); // for test order
        /** @var Award $award */
        $award = Award::factory()->create(['order' => 0]);

        // act
        $response = $this->putJson(route('awards.update', $award), [
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => $award->getTranslation('title', 'th'),
            ],
            'en' => [
                'title' => $award->getTranslation('title', 'en'),
            ],
        ]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('award_translations', ['item_id' => $award->id, 'locale' => 'th', 'title' => $award->getTranslation('title', 'th')]);
        $this->assertDatabaseHas('award_translations', ['item_id' => $award->id, 'locale' => 'en', 'title' => $award->getTranslation('title', 'en')]);
        $response->assertJsonFragment(['status' => AwardStatus::DRAFT]);

        $this->assertDatabaseCount('awards', 3); // existed 2 + created 1
        $this->assertDatabaseHas('awards', [
            'id' => $award->id,
            'order' => 0,
        ]);
    }
}
