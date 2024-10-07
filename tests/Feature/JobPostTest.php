<?php

namespace Tests\Feature;

use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostTest extends TestCase
{
    use refreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_the_admin_can_get_all_blogs(): void
    {
        // set up
        $this->signInAdmin();
        [$jobPostA, $jobPostB] = JobPost::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('careers.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['title' => $jobPostA->title]);
        $response->assertJsonFragment(['title' => $jobPostB->title]);
    }
}
