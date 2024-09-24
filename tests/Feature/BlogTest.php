<?php

namespace Tests\Feature;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use refreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_admin_can_get_all_blogs(): void
    {
        // set up
        $this->signInAdmin();
        [$blogA, $blogB] = Blog::factory()->count(2)->create();

        // act
        $response = $this->get(route('blogs.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['title' => $blogA->title]);
        $response->assertJsonFragment(['title' => $blogB->title]);
    }
}
