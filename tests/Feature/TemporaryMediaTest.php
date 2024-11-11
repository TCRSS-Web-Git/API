<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TemporaryMediaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_upload_temporary_media()
    {
        // Create a user
        $user = User::factory()->create();
        $this->signIn($user);

        // Simulate a file upload
        $file = UploadedFile::fake()->image('media.jpg');

        // Act as the created user
        $response = $this->postJson(route('temporary_media.store'), [
            'media' => $file,
        ]);

        // Assert the response status and structure
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'name',
                'path',
                'url',
            ],
        ]);
    }
}
