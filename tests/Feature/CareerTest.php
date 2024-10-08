<?php

namespace Tests\Feature;

use App\Enums\CareerStatus;
use App\Models\Career;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerTest extends TestCase
{
    use refreshDatabase;

    /**
     * Test the admin can get careers.
     */
    public function test_the_admin_can_get_all_careers(): void
    {
        // set up
        $this->signInAdmin();
        [$careerA, $careerB] = Career::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('careers.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $careerA->hashid]);
        $response->assertJsonFragment(['id' => $careerB->hashid]);
    }

    /**
     * Test the admin can get filtered careers by search.
     */
    public function test_the_admin_can_get_careers_filtered_by_search(): void
    {
        // set up
        $this->signInAdmin();
        [$careerA, $careerB, $careerC] = Career::factory()->count(3)->create();
        $careerA->setTranslation('title', 'test A', 'en');
        $careerA->save();
        $careerB->setTranslation('title', 'B test', 'en');
        $careerB->save();
        $careerC->setTranslation('title', 'career post C', 'en');
        $careerC->save();

        // act
        $response = $this->getJson(route('careers.index', ['search' => 'test']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $careerA->hashid]);
        $response->assertJsonFragment(['id' => $careerB->hashid]);
        $response->assertJsonMissing(['id' => $careerC->hashid]);
    }

    /**
     * Test the admin can get filtered careers by departments.
     */
    public function test_the_admin_can_get_careers_filtered_by_department(): void
    {
        // set up
        $this->signInAdmin();
        $departmentA = Category::factory()->department()->create();
        $departmentB = Category::factory()->department()->create();
        $departmentC = Category::factory()->department()->create();

        $career1 = Career::factory()->create(['department_id' => $departmentA->id]);
        $career2 = Career::factory()->create(['department_id' => $departmentB->id]);
        $career3 = Career::factory()->create(['department_id' => $departmentC->id]);
        $career4 = Career::factory()->create(['department_id' => $departmentA->id]);

        // act
        $response = $this->getJson(route('careers.index', ['department_id' => "$departmentA->hashid,$departmentB->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career1->hashid]);
        $response->assertJsonFragment(['id' => $career2->hashid]);
        $response->assertJsonFragment(['id' => $career4->hashid]);
        $response->assertJsonMissing(['id' => $career3->hashid]);

        // act
        $response = $this->getJson(route('careers.index', ['department_id' => "$departmentC->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career3->hashid]);
        $response->assertJsonMissing(['id' => $career1->hashid]);
        $response->assertJsonMissing(['id' => $career2->hashid]);
        $response->assertJsonMissing(['id' => $career4->hashid]);
    }

    /**
     * Test the admin can get filtered careers by location.
     */
    public function test_the_admin_can_get_careers_filtered_by_location(): void
    {
        // set up
        $this->signInAdmin();
        $locationA = Category::factory()->location()->create();
        $locationB = Category::factory()->location()->create();
        $locationC = Category::factory()->location()->create();

        $career1 = Career::factory()->create(['location_id' => $locationA->id]);
        $career2 = Career::factory()->create(['location_id' => $locationB->id]);
        $career3 = Career::factory()->create(['location_id' => $locationC->id]);
        $career4 = Career::factory()->create(['location_id' => $locationA->id]);

        // act
        $response = $this->getJson(route('careers.index', ['location_id' => "$locationA->hashid,$locationB->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career1->hashid]);
        $response->assertJsonFragment(['id' => $career2->hashid]);
        $response->assertJsonFragment(['id' => $career4->hashid]);
        $response->assertJsonMissing(['id' => $career3->hashid]);

        // act
        $response = $this->getJson(route('careers.index', ['location_id' => "$locationC->hashid"]));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career3->hashid]);
        $response->assertJsonMissing(['id' => $career1->hashid]);
        $response->assertJsonMissing(['id' => $career2->hashid]);
        $response->assertJsonMissing(['id' => $career4->hashid]);
    }

    /**
     * Test the admin can get filtered careers by status.
     */
    public function test_the_admin_can_get_careers_filtered_by_status(): void
    {
        // set up
        Carbon::setTestNow(Carbon::now());
        $this->signInAdmin();
        $career1 = Career::factory()->create(['published_at' => Carbon::now()]);
        $career2 = Career::factory()->create(['published_at' => Carbon::now()->subSecond()]);
        $career3 = Career::factory()->create(['published_at' => Carbon::now()->subDay()]);
        $career4 = Career::factory()->create(['published_at' => Carbon::now()->addSecond()]);
        $career5 = Career::factory()->create(['published_at' => Carbon::now()->addDay()]);

        // act
        $response = $this->getJson(route('careers.index', ['status' => 'published']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career1->hashid]);
        $response->assertJsonFragment(['id' => $career2->hashid]);
        $response->assertJsonFragment(['id' => $career3->hashid]);
        $response->assertJsonMissing(['id' => $career4->hashid]);
        $response->assertJsonMissing(['id' => $career5->hashid]);

        // act
        $response = $this->getJson(route('careers.index', ['status' => 'draft']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career4->hashid]);
        $response->assertJsonFragment(['id' => $career5->hashid]);
        $response->assertJsonMissing(['id' => $career1->hashid]);
        $response->assertJsonMissing(['id' => $career2->hashid]);
        $response->assertJsonMissing(['id' => $career3->hashid]);
    }

    /**
     * Test the admin can get a career by ID.
     */
    public function test_the_admin_can_get_a_career_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $career = Career::factory()->create();

        // act
        $response = $this->getJson(route('careers.show', $career));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career->hashid]);
        $response->assertJsonFragment(['title' => $career->title]);
    }

    /**
     * Test the admin can get a career by ID.
     */
    public function test_the_admin_can_get_a_career_by_id_localized(): void
    {
        // set up
        $this->signInAdmin();
        $career = Career::factory()->create();

        // act Thai
        $response = $this->getJson(route('careers.show', $career), ['X-Localization' => 'th']);

        // assert Thai
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career->hashid]);
        $response->assertJsonFragment(['title' => $career->getTranslation('title', 'th')]);

        // act English
        $response = $this->getJson(route('careers.show', $career), ['X-Localization' => 'en']);

        // assert English
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career->hashid]);
        $response->assertJsonFragment(['title' => $career->getTranslation('title', 'en')]);
    }

    /**
     * Test the admin can get a career by ID with all translations.
     */
    public function test_the_admin_can_get_a_career_by_id_with_all_translations(): void
    {
        // set up
        $this->signInAdmin();
        $career = Career::factory()->create();

        // act
        $response = $this->getJson(route('careers.show', ['career' => $career, 'include' => 'translations']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $career->hashid]);
        $response->assertJsonFragment(['title' => $career->title]);
        $response->assertJsonFragment(['title' => $career->getTranslation('title', 'th')]);
        $response->assertJsonFragment(['title' => $career->getTranslation('title', 'en')]);
        $response->assertJsonStructure(['data' => ['translations' => ['th' => ['title']]]]);
        $response->assertJsonStructure(['data' => ['translations' => ['en' => ['title']]]]);
    }

    public function test_the_admin_can_create_a_published_career(): void
    {
        // set up
        $this->signInAdmin();
        $jobCategory = Category::factory()->department()->create();
        $locationCategory = Category::factory()->location()->create();
        $careerTypeCategory = Category::factory()->careerType()->create();
        $jobData = Career::factory()->make()->toArray();

        // act
        $response = $this->postJson(route('careers.store'), [
            'location_id' => $locationCategory->hashid,
            'department_id' => $jobCategory->hashid,
            'type_id' => $careerTypeCategory->hashid,
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
        $this->assertDatabaseHas('careers', ['type_id' => $careerTypeCategory->id]);
        $this->assertDatabaseHas('careers', ['location_id' => $locationCategory->id]);
        $this->assertDatabaseHas('careers', ['department_id' => $jobCategory->id]);
        $this->assertDatabaseHas('career_translations', ['locale' => 'th', 'title' => 'ชื่อประกาศสมัครงาน']);
        $this->assertDatabaseHas('career_translations', ['locale' => 'en', 'title' => 'Job name']);
        $response->assertJsonFragment(['status' => CareerStatus::PUBLISHED]);
    }

    public function test_the_admin_can_update_a_career(): void
    {
        // set up
        $this->signInAdmin();
        $jobCategory = Category::factory()->department()->create();
        $locationCategory = Category::factory()->location()->create();
        $careerTypeCategory = Category::factory()->careerType()->create();
        $jobData = Career::factory()->create();
        $updatedData = Career::factory()->make()->toArray();

        // act
        $response = $this->putJson(route('careers.update', $jobData), [
            'location_id' => $locationCategory->hashid,
            'department_id' => $jobCategory->hashid,
            'type_id' => $careerTypeCategory->hashid,
            'published_at' => now()->subDay(),
            'th' => [
                'title' => 'ชื่อประกาศสมัครงาน',
                'body' => 'เนื้อหาประกาศสมัครงาน',
                'meta_title' => 'ชื่อประกาศสมัครงาน',
                'meta_description' => 'เนื้อหาประกาศสมัครงาน',
            ],
            'en' => [
                'title' => 'career name',
                'body' => 'career content',
                'meta_title' => 'career name',
                'meta_description' => 'career content',
            ],
        ]);

        // assert
        $this->assertDatabaseHas('careers', ['type_id' => $careerTypeCategory->id]);
        $this->assertDatabaseHas('careers', ['location_id' => $locationCategory->id]);
        $this->assertDatabaseHas('careers', ['department_id' => $jobCategory->id]);
        $this->assertDatabaseHas('career_translations', ['locale' => 'th', 'title' => 'ชื่อประกาศสมัครงาน']);
        $this->assertDatabaseHas('career_translations', ['locale' => 'en', 'title' => 'career name']);
        $response->assertJsonFragment(['status' => CareerStatus::PUBLISHED]);
    }

    public function test_the_admin_can_delete_a_career(): void
    {
        // set up
        $this->signInAdmin();
        $career = Career::factory()->create();

        // act
        $response = $this->deleteJson(route('careers.destroy', $career));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('careers', ['id' => $career->id]);
    }
}
