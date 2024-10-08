<?php

namespace Tests\Feature;

use App\Enums\JobPostStatus;
use App\Models\Category;
use App\Models\JobPost;
use Carbon\Carbon;
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
     * Test the admin can get filtered jobs by search.
     */
    public function test_the_admin_can_get_jobs_filtered_by_search(): void
    {
        // set up
        $this->signInAdmin();
        [$jobPostA, $jobPostB, $jobPostC] = JobPost::factory()->count(3)->create();
        $jobPostA->setTranslation('title', 'test A', 'en');
        $jobPostA->save();
        $jobPostB->setTranslation('title', 'B test', 'en');
        $jobPostB->save();
        $jobPostC->setTranslation('title', 'job post C', 'en');
        $jobPostC->save();

        // act
        $response = $this->getJson(route('careers.index', ['search' => 'test']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPostA->hashid]);
        $response->assertJsonFragment(['id' => $jobPostB->hashid]);
        $response->assertJsonMissing(['id' => $jobPostC->hashid]);
    }

    /**
     * Test the admin can get filtered jobs by departments.
     */
    public function test_the_admin_can_get_jobs_filtered_by_department(): void
    {
        // set up
        $this->signInAdmin();
        $departmentA = Category::factory()->jobPost()->create();
        $departmentB = Category::factory()->jobPost()->create();
        $departmentC = Category::factory()->jobPost()->create();

        $jobPost1 = JobPost::factory()->create(['department_id' => $departmentA->id]);
        $jobPost2 = JobPost::factory()->create(['department_id' => $departmentB->id]);
        $jobPost3 = JobPost::factory()->create(['department_id' => $departmentC->id]);
        $jobPost4 = JobPost::factory()->create(['department_id' => $departmentA->id]);

        // act
        $response = $this->getJson(route('careers.index', ['department_id' => "$departmentA->hashid,$departmentB->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost1->hashid]);
        $response->assertJsonFragment(['id' => $jobPost2->hashid]);
        $response->assertJsonFragment(['id' => $jobPost4->hashid]);
        $response->assertJsonMissing(['id' => $jobPost3->hashid]);

        // act
        $response = $this->getJson(route('careers.index', ['department_id' => "$departmentC->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost3->hashid]);
        $response->assertJsonMissing(['id' => $jobPost1->hashid]);
        $response->assertJsonMissing(['id' => $jobPost2->hashid]);
        $response->assertJsonMissing(['id' => $jobPost4->hashid]);
    }

    /**
     * Test the admin can get filtered jobs by location.
     */
    public function test_the_admin_can_get_jobs_filtered_by_location(): void
    {
        // set up
        $this->signInAdmin();
        $locationA = Category::factory()->location()->create();
        $locationB = Category::factory()->location()->create();
        $locationC = Category::factory()->location()->create();

        $jobPost1 = JobPost::factory()->create(['location_id' => $locationA->id]);
        $jobPost2 = JobPost::factory()->create(['location_id' => $locationB->id]);
        $jobPost3 = JobPost::factory()->create(['location_id' => $locationC->id]);
        $jobPost4 = JobPost::factory()->create(['location_id' => $locationA->id]);

        // act
        $response = $this->getJson(route('careers.index', ['location_id' => "$locationA->hashid,$locationB->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost1->hashid]);
        $response->assertJsonFragment(['id' => $jobPost2->hashid]);
        $response->assertJsonFragment(['id' => $jobPost4->hashid]);
        $response->assertJsonMissing(['id' => $jobPost3->hashid]);

        // act
        $response = $this->getJson(route('careers.index', ['location_id' => "$locationC->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost3->hashid]);
        $response->assertJsonMissing(['id' => $jobPost1->hashid]);
        $response->assertJsonMissing(['id' => $jobPost2->hashid]);
        $response->assertJsonMissing(['id' => $jobPost4->hashid]);
    }

    /**
     * Test the admin can get filtered jobs by status.
     */
    public function test_the_admin_can_get_jobs_filtered_by_status(): void
    {
        // set up
        Carbon::setTestNow(Carbon::now());
        $this->signInAdmin();
        $jobPost1 = JobPost::factory()->create(['published_at' => Carbon::now()]);
        $jobPost2 = JobPost::factory()->create(['published_at' => Carbon::now()->subSecond()]);
        $jobPost3 = JobPost::factory()->create(['published_at' => Carbon::now()->subDay()]);
        $jobPost4 = JobPost::factory()->create(['published_at' => Carbon::now()->addSecond()]);
        $jobPost5 = JobPost::factory()->create(['published_at' => Carbon::now()->addDay()]);

        // act
        $response = $this->getJson(route('careers.index', ['status' => 'published']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost1->hashid]);
        $response->assertJsonFragment(['id' => $jobPost2->hashid]);
        $response->assertJsonFragment(['id' => $jobPost3->hashid]);
        $response->assertJsonMissing(['id' => $jobPost4->hashid]);
        $response->assertJsonMissing(['id' => $jobPost5->hashid]);

        // act
        $response = $this->getJson(route('careers.index', ['status' => 'draft']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost4->hashid]);
        $response->assertJsonFragment(['id' => $jobPost5->hashid]);
        $response->assertJsonMissing(['id' => $jobPost1->hashid]);
        $response->assertJsonMissing(['id' => $jobPost2->hashid]);
        $response->assertJsonMissing(['id' => $jobPost3->hashid]);
    }

    /**
     * Test the admin can get a job by ID.
     */
    public function test_the_admin_can_get_a_job_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $jobPost = JobPost::factory()->create();

        // act
        $response = $this->getJson(route('careers.show', $jobPost));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost->hashid]);
        $response->assertJsonFragment(['title' => $jobPost->title]);
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

    /**
     * Test the admin can get a job by ID with all translations.
     */
    public function test_the_admin_can_get_a_job_by_id_with_all_translations(): void
    {
        // set up
        $this->signInAdmin();
        $jobPost = JobPost::factory()->create();

        // act
        $response = $this->getJson(route('careers.show', ['career' => $jobPost, 'include' => 'translations']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $jobPost->hashid]);
        $response->assertJsonFragment(['title' => $jobPost->title]);
        $response->assertJsonFragment(['title' => $jobPost->getTranslation('title', 'th')]);
        $response->assertJsonFragment(['title' => $jobPost->getTranslation('title', 'en')]);
        $response->assertJsonStructure(['data' => ['translations' => ['th' => ['title']]]]);
        $response->assertJsonStructure(['data' => ['translations' => ['en' => ['title']]]]);
    }

    public function test_the_admin_can_create_a_published_job_post(): void
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
        $this->assertDatabaseHas('job_posts', ['location_id' => $locationCategory->id]);
        $this->assertDatabaseHas('job_posts', ['department_id' => $jobCategory->id]);
        $this->assertDatabaseHas('job_post_translations', ['locale' => 'th', 'title' => 'ชื่อประกาศสมัครงาน']);
        $this->assertDatabaseHas('job_post_translations', ['locale' => 'en', 'title' => 'Job name']);
        $response->assertJsonFragment(['status' => JobPostStatus::PUBLISHED]);
    }

    public function test_the_admin_can_update_a_job_post(): void
    {
        // set up
        $this->signInAdmin();
        $jobCategory = Category::factory()->jobPost()->create();
        $locationCategory = Category::factory()->location()->create();
        $jobData = JobPost::factory()->create();
        $updatedData = JobPost::factory()->make()->toArray();

        // act
        $response = $this->putJson(route('careers.update', $jobData), [
            'location_id' => $locationCategory->id,
            'department_id' => $jobCategory->id,
            'type' => $updatedData['type'],
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

        $this->assertDatabaseHas('job_posts', ['type' => $updatedData['type']]);
        $this->assertDatabaseHas('job_posts', ['location_id' => $locationCategory->id]);
        $this->assertDatabaseHas('job_posts', ['department_id' => $jobCategory->id]);
        $this->assertDatabaseHas('job_post_translations', ['locale' => 'th', 'title' => 'ชื่อประกาศสมัครงาน']);
        $this->assertDatabaseHas('job_post_translations', ['locale' => 'en', 'title' => 'Job name']);
        $response->assertJsonFragment(['status' => JobPostStatus::PUBLISHED]);
    }

    public function test_the_admin_can_delete_a_job_post(): void
    {
        // set up
        $this->signInAdmin();
        $jobPost = JobPost::factory()->create();

        // act
        $response = $this->deleteJson(route('careers.destroy', $jobPost));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('job_posts', ['id' => $jobPost->id]);
    }
}
