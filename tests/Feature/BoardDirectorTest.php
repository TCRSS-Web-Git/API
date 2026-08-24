<?php

namespace Tests\Feature;

use App\Enums\BoardDirectorStatus;
use App\Models\BoardDirector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BoardDirectorTest extends TestCase
{
    use RefreshDatabase;

    private function setupImage(): array
    {
        $file = UploadedFile::fake()->image('board_director.jpg');
        $media = $this->postJson(route('temporary_media.store'), ['media' => $file]);

        return $media->json('data');
    }

    private function payload(array $overrides = []): array
    {
        $image = $this->setupImage();

        return array_merge([
            'th' => [
                'name' => 'ชื่อกรรมการ',
                'position' => 'ตำแหน่งกรรมการ',
            ],
            'en' => [
                'name' => 'Director name',
                'position' => 'Director position',
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

    public function test_the_admin_can_get_all_board_directors(): void
    {
        // set up
        $this->signInAdmin();
        [$directorA, $directorB] = BoardDirector::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('board-directors.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['name' => $directorA->name]);
        $response->assertJsonFragment(['name' => $directorB->name]);
    }

    public function test_the_admin_can_get_a_board_director_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $director = BoardDirector::factory()->create();

        // act
        $response = $this->getJson(route('board-directors.show', $director));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $director->hashid]);
        $response->assertJsonFragment(['name' => $director->name]);
    }

    public function test_the_admin_can_create_a_draft_board_director(): void
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
        $response = $this->postJson(route('board-directors.store'), $this->payload($createdPayload));

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => BoardDirectorStatus::DRAFT]);
        $this->assertDatabaseHas('board_directors', ['id' => 1, 'published_at' => null]);
        $this->assertDatabaseHas('board_director_translations', ['locale' => 'th', 'name' => $createdPayload['th']['name'], 'position' => $createdPayload['th']['position']]);
        $this->assertDatabaseHas('board_director_translations', ['locale' => 'en', 'name' => $createdPayload['en']['name'], 'position' => $createdPayload['en']['position']]);
        $this->assertDatabaseCount('board_directors', 1);
    }

    public function test_the_admin_can_update_a_draft_board_director(): void
    {
        // set up
        $this->signInAdmin();
        $director = BoardDirector::factory()->draft()->create();

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
        $response = $this->putJson(route('board-directors.update', $director), $this->payload($updatedPayload));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => BoardDirectorStatus::DRAFT]);
        $this->assertDatabaseHas('board_directors', ['id' => 1, 'published_at' => null]);
        $this->assertDatabaseHas('board_director_translations', ['item_id' => $director->id, 'locale' => 'th', 'name' => $updatedPayload['th']['name'], 'position' => $updatedPayload['th']['position'] ]);
        $this->assertDatabaseHas('board_director_translations', ['item_id' => $director->id, 'locale' => 'en', 'name' => $updatedPayload['en']['name'], 'position' => $updatedPayload['en']['position'] ]);
        $this->assertDatabaseCount('board_directors', 1);
    }

    public function test_the_admin_can_delete_a_draft_board_director(): void
    {
        // set up
        $this->signInAdmin();
        $director = BoardDirector::factory()->draft()->create();

        // act
        $response = $this->deleteJson(route('board-directors.destroy', $director));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('board_directors', ['id' => $director->id]);
    }

    public function test_the_admin_cannot_create_a_published_board_director(): void
    {
        // set up
        $this->signInAdmin();

        // act
        $response = $this->postJson(route('board-directors.store'), $this->payload([
            'published_at' => now(),
        ]));

        // assert
        $response->assertForbidden();
        $response->assertJsonFragment(['message' => 'You do not have permission to create a published board director.']);
        $this->assertDatabaseCount('board_directors', 0);
    }

    public function test_the_admin_cannot_update_a_published_board_director(): void
    {
        // set up
        $this->signInAdmin();
        $director = BoardDirector::factory()->draft()->create();

        // act
        $response = $this->putJson(route('board-directors.update', $director), $this->payload([
            'published_at' => now(),
        ]));

        // assert
        $response->assertForbidden();
        $response->assertJsonFragment(['message' => 'You do not have permission to update a published board director.']);
    }

    public function test_the_admin_cannot_delete_a_published_board_director(): void
    {
        // set up
        $this->signInAdmin();
        $director = BoardDirector::factory()->published()->create();

        // act
        $response = $this->deleteJson(route('board-directors.destroy', $director));

        // assert
        $response->assertForbidden();
        $response->assertJsonFragment(['message' => 'You do not have permission to delete a published board director.']);
        $this->assertDatabaseHas('board_directors', ['id' => $director->id]);
    }

    /* ---------------------------------------------------------------------
     | Super Admin
     | -------------------------------------------------------------------- */

    public function test_the_super_admin_can_get_all_board_directors(): void
    {
        // set up
        $this->signInSuperAdmin();
        [$directorA, $directorB] = BoardDirector::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('board-directors.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['name' => $directorA->name]);
        $response->assertJsonFragment(['name' => $directorB->name]);
    }

    public function test_the_super_admin_can_get_a_board_director_by_id(): void
    {
        // set up
        $this->signInSuperAdmin();
        $director = BoardDirector::factory()->create();

        // act
        $response = $this->getJson(route('board-directors.show', $director));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $director->hashid]);
        $response->assertJsonFragment(['name' => $director->name]);
    }

    public function test_the_super_admin_can_create_a_draft_board_director(): void
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
        $response = $this->postJson(route('board-directors.store'), $this->payload($createdPayload));

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => BoardDirectorStatus::DRAFT]);
        $this->assertDatabaseHas('board_directors', ['id' => 1, 'published_at' => null]);
        $this->assertDatabaseHas('board_director_translations', ['locale' => 'th', 'name' => $createdPayload['th']['name'], 'position' => $createdPayload['th']['position']]);
        $this->assertDatabaseHas('board_director_translations', ['locale' => 'en', 'name' => $createdPayload['en']['name'], 'position' => $createdPayload['en']['position']]);
        $this->assertDatabaseCount('board_directors', 1);
    }

    public function test_the_super_admin_can_update_a_draft_board_director(): void
    {
        // set up
        $this->signInSuperAdmin();
        $director = BoardDirector::factory()->draft()->create();

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
        $response = $this->putJson(route('board-directors.update', $director), $this->payload($updatedPayload));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => BoardDirectorStatus::DRAFT]);
        $this->assertDatabaseHas('board_director_translations', ['item_id' => $director->id, 'locale' => 'en', 'name' => $updatedPayload['en']['name'], 'position' => $updatedPayload['en']['position']]);
        $this->assertDatabaseHas('board_director_translations', ['item_id' => $director->id, 'locale' => 'th', 'name' => $updatedPayload['th']['name'], 'position' => $updatedPayload['th']['position']]);
        $this->assertDatabaseCount('board_directors', 1);
    }

    public function test_the_super_admin_can_delete_a_draft_board_director(): void
    {
        // set up
        $this->signInSuperAdmin();
        $director = BoardDirector::factory()->draft()->create();

        // act
        $response = $this->deleteJson(route('board-directors.destroy', $director));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('board_directors', ['id' => $director->id]);
    }

    public function test_the_super_admin_can_create_a_published_board_director(): void
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
        $response = $this->postJson(route('board-directors.store'), $this->payload($createdPayload));

        // assert
        $response->assertCreated();
        $response->assertJsonFragment(['status' => BoardDirectorStatus::PUBLISHED]);
        $this->assertDatabaseHas('board_directors', ['id' => 1, 'published_at' => $createdPayload['published_at']]);
        $this->assertDatabaseHas('board_director_translations', ['locale' => 'th', 'name' => $createdPayload['th']['name'], 'position' => $createdPayload['th']['position']]);
        $this->assertDatabaseHas('board_director_translations', ['locale' => 'en', 'name' => $createdPayload['en']['name'], 'position' => $createdPayload['en']['position']]);
        $this->assertDatabaseCount('board_directors', 1);
    }

    public function test_the_super_admin_can_update_a_published_board_director(): void
    {
        // set up
        $this->signInSuperAdmin();
        $director = BoardDirector::factory()->draft()->create();

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
        $response = $this->putJson(route('board-directors.update', $director), $this->payload($updatedPayload));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['status' => BoardDirectorStatus::PUBLISHED]);
        $this->assertDatabaseHas('board_directors', ['id' => 1, 'published_at' => $updatedPayload['published_at']]);
        $this->assertDatabaseHas('board_director_translations', ['item_id' => $director->id, 'locale' => 'th', 'name' => $updatedPayload['th']['name'], 'position' => $updatedPayload['th']['position'] ]);
        $this->assertDatabaseHas('board_director_translations', ['item_id' => $director->id, 'locale' => 'en', 'name' => $updatedPayload['en']['name'], 'position' => $updatedPayload['en']['position'] ]);
        $this->assertDatabaseCount('board_directors', 1);
   
    }

    public function test_the_super_admin_can_delete_a_published_board_director(): void
    {
        // set up
        $this->signInSuperAdmin();
        $director = BoardDirector::factory()->published()->create();

        // act
        $response = $this->deleteJson(route('board-directors.destroy', $director));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('board_directors', ['id' => $director->id]);
    }
}
