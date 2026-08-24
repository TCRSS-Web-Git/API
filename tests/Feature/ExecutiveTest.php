<?php

namespace Tests\Feature;

use App\Enums\ExecutiveStatus;
use App\Models\Executive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExecutiveTest extends TestCase
{
    use RefreshDatabase;

    private function setupImage(): array
    {
        $file = UploadedFile::fake()->image('executive.jpg');
        $media = $this->postJson(route('temporary_media.store'), ['media' => $file]);

        return $media->json('data');
    }

    private function payload(array $overrides = []): array
    {
        $image = $this->setupImage();

        return array_merge([
            'th' => [
                'name' => 'ชื่อผู้บริหาร',
                'position' => 'ตำแหน่งผู้บริหาร',
            ],
            'en' => [
                'name' => 'Executive name',
                'position' => 'Executive position',
            ],
            'image' => [
                'id' => null,
                'path' => $image['path'],
            ],
        ], $overrides);
    }

    /* ---------------------------------------------------------------------
     | Admin
     | -------------------------------------------------------------------- */

    public function test_the_admin_can_get_all_executives(): void
    {
        // set up
        $this->signInAdmin();
        [$executiveA, $executiveB] = Executive::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('executives.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['name' => $executiveA->name]);
        $response->assertJsonFragment(['name' => $executiveB->name]);
    }

    public function test_the_admin_can_get_an_executive_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $executive = Executive::factory()->create();

        // act
        $response = $this->getJson(route('executives.show', $executive));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $executive->hashid]);
        $response->assertJsonFragment(['name' => $executive->name]);
    }

    public function test_the_admin_can_create_a_draft_executive(): void
    {
        // set up
        $this->signInAdmin();

        // act
        $createdPayload = [
            'published_at' => null,
            'th' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
            'en' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
        ];
        $response = $this->postJson(route('executives.store'), $this->payload($createdPayload));

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => ExecutiveStatus::DRAFT]);
        $this->assertDatabaseHas('executives', ['id' => 1, 'published_at' => null]);
        $this->assertDatabaseHas('executive_translations', ['locale' => 'th', 'name' => $createdPayload['th']['name'], 'position' => $createdPayload['th']['position']]);
        $this->assertDatabaseHas('executive_translations', ['locale' => 'en', 'name' => $createdPayload['en']['name'], 'position' => $createdPayload['en']['position']]);
        $this->assertDatabaseCount('executives', 1);
    }

    public function test_the_admin_can_update_a_draft_executive(): void
    {
        // set up
        $this->signInAdmin();
        $executive = Executive::factory()->draft()->create();

        // act
        $updatedPayload = [
            'published_at' => null,
            'th' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
            'en' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
        ];
        $response = $this->putJson(route('executives.update', $executive), $this->payload($updatedPayload));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => ExecutiveStatus::DRAFT]);
        $this->assertDatabaseHas('executives', ['id' => 1, 'published_at' => null]);
        $this->assertDatabaseHas('executive_translations', ['item_id' => $executive->id, 'locale' => 'th', 'name' => $updatedPayload['th']['name'], 'position' => $updatedPayload['th']['position']]);
        $this->assertDatabaseHas('executive_translations', ['item_id' => $executive->id, 'locale' => 'en', 'name' => $updatedPayload['en']['name'], 'position' => $updatedPayload['en']['position']]);
        $this->assertDatabaseCount('executives', 1);
    }

    public function test_the_admin_can_delete_a_draft_executive(): void
    {
        // set up
        $this->signInAdmin();
        $executive = Executive::factory()->draft()->create();

        // act
        $response = $this->deleteJson(route('executives.destroy', $executive));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('executives', ['id' => $executive->id]);
    }

    public function test_the_admin_cannot_create_a_published_executive(): void
    {
        // set up
        $this->signInAdmin();

        // act
        $response = $this->postJson(route('executives.store'), $this->payload([
            'published_at' => now(),
        ]));

        // assert
        $response->assertForbidden();
        $response->assertJsonFragment(['message' => 'You do not have permission to create a published executive.']);
        $this->assertDatabaseCount('executives', 0);
    }

    public function test_the_admin_cannot_update_a_published_executive(): void
    {
        // set up
        $this->signInAdmin();
        $executive = Executive::factory()->draft()->create();

        // act
        $response = $this->putJson(route('executives.update', $executive), $this->payload([
            'published_at' => now(),
        ]));

        // assert
        $response->assertForbidden();
        $response->assertJsonFragment(['message' => 'You do not have permission to update a published executive.']);
    }

    public function test_the_admin_cannot_delete_a_published_executive(): void
    {
        // set up
        $this->signInAdmin();
        $executive = Executive::factory()->published()->create();

        // act
        $response = $this->deleteJson(route('executives.destroy', $executive));

        // assert
        $response->assertForbidden();
        $response->assertJsonFragment(['message' => 'You do not have permission to delete a published executive.']);
        $this->assertDatabaseHas('executives', ['id' => $executive->id]);
    }

    /* ---------------------------------------------------------------------
     | Super Admin
     | -------------------------------------------------------------------- */

    public function test_the_super_admin_can_get_all_executives(): void
    {
        // set up
        $this->signInSuperAdmin();
        [$executiveA, $executiveB] = Executive::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('executives.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['name' => $executiveA->name]);
        $response->assertJsonFragment(['name' => $executiveB->name]);
    }

    public function test_the_super_admin_can_get_an_executive_by_id(): void
    {
        // set up
        $this->signInSuperAdmin();
        $executive = Executive::factory()->create();

        // act
        $response = $this->getJson(route('executives.show', $executive));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $executive->hashid]);
        $response->assertJsonFragment(['name' => $executive->name]);
    }

    public function test_the_super_admin_can_create_a_draft_executive(): void
    {
        // set up
        $this->signInSuperAdmin();

        // act
        $createdPayload = [
            'published_at' => null,
            'th' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
            'en' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
        ];
        $response = $this->postJson(route('executives.store'), $this->payload($createdPayload));

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => ExecutiveStatus::DRAFT]);
        $this->assertDatabaseHas('executives', ['id' => 1, 'published_at' => null]);
        $this->assertDatabaseHas('executive_translations', ['locale' => 'th', 'name' => $createdPayload['th']['name'], 'position' => $createdPayload['th']['position']]);
        $this->assertDatabaseHas('executive_translations', ['locale' => 'en', 'name' => $createdPayload['en']['name'], 'position' => $createdPayload['en']['position']]);
        $this->assertDatabaseCount('executives', 1);
    }

    public function test_the_super_admin_can_update_a_draft_executive(): void
    {
        // set up
        $this->signInSuperAdmin();
        $executive = Executive::factory()->draft()->create();

        // act
        $updatedPayload = [
            'published_at' => null,
            'th' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
            'en' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
        ];
        $response = $this->putJson(route('executives.update', $executive), $this->payload($updatedPayload));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => ExecutiveStatus::DRAFT]);
        $this->assertDatabaseHas('executive_translations', ['item_id' => $executive->id, 'locale' => 'en', 'name' => $updatedPayload['en']['name'], 'position' => $updatedPayload['en']['position']]);
        $this->assertDatabaseHas('executive_translations', ['item_id' => $executive->id, 'locale' => 'th', 'name' => $updatedPayload['th']['name'], 'position' => $updatedPayload['th']['position']]);
        $this->assertDatabaseCount('executives', 1);
    }

    public function test_the_super_admin_can_delete_a_draft_executive(): void
    {
        // set up
        $this->signInSuperAdmin();
        $executive = Executive::factory()->draft()->create();

        // act
        $response = $this->deleteJson(route('executives.destroy', $executive));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('executives', ['id' => $executive->id]);
    }

    public function test_the_super_admin_can_create_a_published_executive(): void
    {
        // set up
        $this->signInSuperAdmin();

        // act
        $createdPayload = [
            'published_at' => now(),
            'th' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
            'en' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
        ];
        $response = $this->postJson(route('executives.store'), $this->payload($createdPayload));

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => ExecutiveStatus::PUBLISHED]);
        $this->assertDatabaseHas('executives', ['id' => 1, 'published_at' => $createdPayload['published_at']]);
        $this->assertDatabaseHas('executive_translations', ['locale' => 'th', 'name' => $createdPayload['th']['name'], 'position' => $createdPayload['th']['position']]);
        $this->assertDatabaseHas('executive_translations', ['locale' => 'en', 'name' => $createdPayload['en']['name'], 'position' => $createdPayload['en']['position']]);
        $this->assertDatabaseCount('executives', 1);
    }

    public function test_the_super_admin_can_update_a_published_executive(): void
    {
        // set up
        $this->signInSuperAdmin();
        $executive = Executive::factory()->draft()->create();

        // act
        $updatedPayload = [
            'published_at' => now(),
            'th' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
            'en' => [
                'name' => fake()->name(),
                'position' => fake()->name(),
            ],
        ];
        $response = $this->putJson(route('executives.update', $executive), $this->payload($updatedPayload));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => ExecutiveStatus::PUBLISHED]);
        $this->assertDatabaseHas('executives', ['id' => 1, 'published_at' => $updatedPayload['published_at']]);
        $this->assertDatabaseHas('executive_translations', ['item_id' => $executive->id, 'locale' => 'th', 'name' => $updatedPayload['th']['name'], 'position' => $updatedPayload['th']['position']]);
        $this->assertDatabaseHas('executive_translations', ['item_id' => $executive->id, 'locale' => 'en', 'name' => $updatedPayload['en']['name'], 'position' => $updatedPayload['en']['position']]);
        $this->assertDatabaseCount('executives', 1);
    }

    public function test_the_super_admin_can_delete_a_published_executive(): void
    {
        // set up
        $this->signInSuperAdmin();
        $executive = Executive::factory()->published()->create();

        // act
        $response = $this->deleteJson(route('executives.destroy', $executive));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('executives', ['id' => $executive->id]);
    }
}
