<?php

namespace Tests\Feature;

use App\Enums\JobPostStatus;
use App\Models\Category;
use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostTest extends TestCase
{
    use refreshDatabase;

    /**
     * A basic feature test example.
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
        $response->assertJsonFragment(['title' => $jobPostA->title]);
        $response->assertJsonFragment(['title' => $jobPostB->title]);
    }

    public function test_the_admin_can_create_a_published_job(): void
    {
        // set up
        $this->signInAdmin();
        $jobCategory = Category::factory()->jobPost()->create();
        $locationCategory = Category::factory()->location()->create();
        $jobData = JobPost::factory()->make()->toArray();

        // act
        $response = $this->postJson(route('careers.store'), [
            'location_id' => $locationCategory->id,
            'department_id' => $jobCategory->id,
            'type' => $jobData['type'],
            'published_at' => now()->subDay(),
            'th' => [
                'title' => 'ชื่อประกาศสมัครงาน',
                'body' => 'เนื้อหาประกาศสมัครงาน',
                'meta_title' => 'ชื่อประกาศสมัครงาน',
                'meta_description' => 'เนื้อหาประกาศสมัครงาน',
            ],
            'en' => [
                'title' => 'Job name',
                'body' => 'Job content',
                'meta_title' => 'Job name',
                'meta_description' => 'Job content',
            ],
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseHas('job_posts', ['type' => $jobData['type']]);
        $this->assertDatabaseHas('job_post_translations', ['locale' => 'th', 'title' => 'ชื่อประกาศสมัครงาน']);
        $this->assertDatabaseHas('job_post_translations', ['locale' => 'en', 'title' => 'Job name']);
        $response->assertJsonFragment(['status' => JobPostStatus::PUBLISHED]);
    }
}
