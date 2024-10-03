<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_tags_by_type()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->signIn($user);

        $blog = Blog::factory()->create();
        $blog->syncTags(['hello', '123']);

        // Make a GET request to the tags.index route
        $response = $this->getJson(route('tags.index', ['type' => Blog::getTagType()]));

        // Assert the response status and structure
        $response->assertStatus(200);
        $response->assertSee('hello');
        $response->assertSee('123');
    }
}
