<?php

namespace Tests\Feature;

use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostTest extends TestCase
{
    use refreshDatabase;

    /**
     * Test the admin can get jobs.
     */
    public function test_the_admin_can_get_all_jobs(): void
    {
        // set up
        $this->signInAdmin();
        [$jobPostA, $jobPostB] = JobPost::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('careers.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPostA->hashid]);
        $response->assertJsonFragment(['id' => $jobPostB->hashid]);
    }

    /**
     * Test the admin can get a job by ID.
     */
    public function test_the_admin_can_get_a_job_by_id_localized(): void
    {
        // set up
        $this->signInAdmin();
        $jobPost = JobPost::factory()->create();

        // act Thai
        $response = $this->getJson(route('careers.show', $jobPost), ['X-Localization' => 'th']);

        // assert Thai
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost->hashid]);
        $response->assertJsonFragment(['title' => $jobPost->getTranslation('title', 'th')]);

        // act English
        $response = $this->getJson(route('careers.show', $jobPost), ['X-Localization' => 'en']);

        // assert English
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost->hashid]);
        $response->assertJsonFragment(['title' => $jobPost->getTranslation('title', 'en')]);
    }
}
