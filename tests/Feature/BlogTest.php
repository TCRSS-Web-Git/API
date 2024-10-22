<?php

namespace Tests\Feature;

use App\Enums\BlogStatus;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Media;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use refreshDatabase;

    public function test_the_admin_can_get_all_blogs(): void
    {
        // set up
        $this->signInAdmin();
        [$blogA, $blogB] = Blog::factory()->count(2)->create();

        // act
        $response = $this->getJson(route('blogs.index'));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['title' => $blogA->title]);
        $response->assertJsonFragment(['title' => $blogB->title]);
    }

    /**
     * Test the admin can create a published blog.
     */
    public function test_the_admin_can_create_a_published_blog(): void
    {
        // set up
        $this->signInAdmin();
        $category = Category::factory()->blog()->create();
        $blogData = Blog::factory()->for($category)->make()->toArray();

        [$cover, $thumbnail] = $this->setupImages();

        // act
        $response = $this->postJson(route('blogs.store'), [
            'category_id' => $category->hashid,
            'slug' => $blogData['slug'],
            'published_at' => now()->subDay(),
            'th' => [
                'title' => 'ชื่อบทความ',
                'body' => 'เนื้อหาบทความ',
                'meta_title' => 'ชื่อบทความ',
                'meta_description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'body' => 'Blog content',
                'meta_title' => 'Blog name',
                'meta_description' => 'Blog content',
            ],
            'cover' => [
                'id' => null,
                'path' => $cover['path'],
            ],
            'thumbnail' => [
                'id' => null,
                'path' => $thumbnail['path'],
            ],
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseHas('blogs', ['slug' => $blogData['slug']]);
        $this->assertDatabaseHas('blog_translations', ['locale' => 'th', 'title' => 'ชื่อบทความ']);
        $this->assertDatabaseHas('blog_translations', ['locale' => 'en', 'title' => 'Blog name']);
        $response->assertJsonFragment(['status' => BlogStatus::PUBLISHED]);

        $this->assertDatabaseCount('blogs', 1);
        $this->assertDatabaseCount('media', 2);
        $blogId = Blog::decodeHash($response->json('data.id'));
        $this->assertDatabaseHas('media', [
            'model_type' => Blog::class,
            'model_id' => $blogId,
            'collection_name' => Blog::MEDIA_COLLECTION_COVER,
            'file_name' => $cover['name'],
        ]);
        $this->assertDatabaseHas('media', [
            'model_type' => Blog::class,
            'model_id' => $blogId,
            'collection_name' => Blog::MEDIA_COLLECTION_THUMBNAIL,
            'file_name' => $thumbnail['name'],
        ]);
    }

    private function setupImages()
    {
        $fileCover = UploadedFile::fake()->image('image_cover.jpg');
        $fileThumbnail = UploadedFile::fake()->image('image_thumbnail.jpg');
        $mediaImageCover = $this->postJson(route('temporary_media.store'), ['media' => $fileCover]);
        $mediaImageThumbnail = $this->postJson(route('temporary_media.store'), ['media' => $fileThumbnail]);

        return [$mediaImageCover->json('data'), $mediaImageThumbnail->json('data')];
    }

    /**
     * Test the admin cannot create a published blog when data is required.
     */
    public function test_the_admin_cannot_create_a_published_blog_when_data_is_required(): void
    {
        // set up
        $this->signInAdmin();
        $category = Category::factory()->blog()->create();

        // act
        $response = $this->postJson(route('blogs.store'), [
            'published_at' => now()->subDay(),
        ]);

        // assert
        $response->assertUnprocessable();
        $this->assertDatabaseCount('blogs', 0);
    }

    /**
     * Test the admin can sort blogs.
     */
    public function test_the_admin_can_sort_blogs(): void
    {
        // set up
        $this->signInAdmin();
        $categoryA = Category::factory()->blog()->create();
        $categoryA->setTranslation('name', 'A', 'en');
        $categoryA->setTranslation('name', 'ก', 'th');
        $categoryA->save();
        $categoryB = Category::factory()->blog()->create();
        $categoryB->setTranslation('name', 'B', 'en');
        $categoryB->setTranslation('name', 'ข', 'th');
        $categoryB->save();
        $categoryC = Category::factory()->blog()->create();
        $categoryC->setTranslation('name', 'C', 'en');
        $categoryC->setTranslation('name', 'ค', 'th');
        $categoryC->save();
        $blog1 = Blog::factory()->create(['category_id' => $categoryA->id]);
        $blog2 = Blog::factory()->create(['category_id' => $categoryB->id]);
        $blog3 = Blog::factory()->create(['category_id' => $categoryC->id]);

        // act
        $response = $this->getJson(route('blogs.index', ['sort' => '-category_id']));

        // assert
        $response->assertOk();
        $result = $response->json('data');
        $this->assertEquals($blog3->hashid, $result[0]['id']);
        $this->assertEquals($blog2->hashid, $result[1]['id']);
        $this->assertEquals($blog1->hashid, $result[2]['id']);

        // act
        $response = $this->getJson(route('blogs.index', ['sort' => 'category_id']));

        // assert
        $response->assertOk();
        $result = $response->json('data');
        $this->assertEquals($blog1->hashid, $result[0]['id']);
        $this->assertEquals($blog2->hashid, $result[1]['id']);
        $this->assertEquals($blog3->hashid, $result[2]['id']);
    }

    /**
     * Test the admin can create a blog.
     */
    public function test_the_admin_can_create_a_blog_for_future_published(): void
    {
        // set up
        $this->signInAdmin();
        $category = Category::factory()->blog()->create();
        $blogData = Blog::factory()->for($category)->make()->toArray();

        // act
        $response = $this->postJson(route('blogs.store'), [
            'category_id' => $category->hashid,
            'slug' => $blogData['slug'],
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => 'ชื่อบทความ',
                'body' => 'เนื้อหาบทความ',
                'meta_title' => 'ชื่อบทความ',
                'meta_description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'body' => 'Blog content',
                'meta_title' => 'Blog name',
                'meta_description' => 'Blog content',
            ],
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseHas('blogs', ['slug' => $blogData['slug']]);
        $this->assertDatabaseHas('blog_translations', ['locale' => 'th', 'title' => 'ชื่อบทความ']);
        $this->assertDatabaseHas('blog_translations', ['locale' => 'en', 'title' => 'Blog name']);
        $response->assertJsonFragment(['status' => BlogStatus::DRAFT]);
        $this->assertDatabaseCount('blogs', 1);
    }

    public function test_the_admin_can_create_a_blog_for_null_in_key_published(): void
    {
        // set up
        $this->signInAdmin();
        $category = Category::factory()->blog()->create();
        $blogData = Blog::factory()->for($category)->make()->toArray();

        // act
        $response = $this->postJson(route('blogs.store'), [
            'category_id' => $category->hashid,
            'slug' => $blogData['slug'],
            'published_at' => null,
            'th' => [
                'title' => 'ชื่อบทความ',
                'body' => 'เนื้อหาบทความ',
                'meta_title' => 'ชื่อบทความ',
                'meta_description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'body' => 'Blog content',
                'meta_title' => 'Blog name',
                'meta_description' => 'Blog content',
            ],
        ]);

        // assert
        $response->assertCreated();
        $this->assertDatabaseHas('blogs', ['slug' => $blogData['slug']]);
        $this->assertDatabaseHas('blog_translations', ['locale' => 'th', 'title' => 'ชื่อบทความ']);
        $this->assertDatabaseHas('blog_translations', ['locale' => 'en', 'title' => 'Blog name']);
        $response->assertJsonFragment(['status' => BlogStatus::DRAFT]);
        $this->assertDatabaseCount('blogs', 1);
    }

    /**
     * Test the admin can get a blog by ID.
     */
    public function test_the_admin_can_get_a_blog_by_id(): void
    {
        // set up
        $this->signInAdmin();
        $blog = Blog::factory()->create();

        // act
        $response = $this->getJson(route('blogs.show', $blog));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $blog->hashid]);
        $response->assertJsonFragment(['title' => $blog->title]);
    }

    public function test_the_admin_can_get_a_blog_by_id_with_other_blogs(): void
    {
        // set up
        $this->signInAdmin();
        $category = Category::factory()->blog()->create();
        Blog::factory()->create();
        $blogData = Blog::factory()->for($category)->make()->toArray();

        // act
        $this->postJson(route('blogs.store'), [
            'category_id' => $category->hashid,
            'slug' => $blogData['slug'],
            'published_at' => now()->addMonth(),
            'tags' => ['test'],
            'th' => [
                'title' => 'ชื่อบทความ',
                'body' => 'เนื้อหาบทความ',
                'meta_title' => 'ชื่อบทความ',
                'meta_description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'body' => 'Blog content',
                'meta_title' => 'Blog name',
                'meta_description' => 'Blog content',
            ],
        ])->assertCreated();
        $this->postJson(route('blogs.store'), [
            'category_id' => $category->hashid,
            'slug' => $blogData['slug'],
            'published_at' => now()->addMonth(),
            'tags' => ['test'],
            'th' => [
                'title' => 'ชื่อบทความ',
                'body' => 'เนื้อหาบทความ',
                'meta_title' => 'ชื่อบทความ',
                'meta_description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'body' => 'Blog content',
                'meta_title' => 'Blog name',
                'meta_description' => 'Blog content',
            ],
        ])->assertCreated();

        // act
        $blog = Blog::all()->first();
        $response = $this->getJson(route('public.blogs.show', $blog));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $blog->hashid]);
        $response->assertJsonFragment(['title' => $blog->title]);
        $this->assertEquals(3, count($response->json('other_blogs')));
    }

    public function test_user_can_get_a_blog_by_id_with_other_blogs(): void
    {
        // set up
        $category = Category::factory()->blog()->create();
        $tagA = Tag::factory()->create();
        $tagB = Tag::factory()->create();
        $blogA = Blog::factory()->for($category)->create(['created_at' => '2024-03-01']);
        $blogB = Blog::factory()->for($category)->create(['created_at' => '2024-03-02']);
        $blogC = Blog::factory()->for($category)->create(['created_at' => '2024-03-03']);
        $blogA->syncTags([$tagA->name]);
        $blogB->syncTags([$tagA->name]);
        $blogC->syncTags([$tagA->name]);
        $blogD = Blog::factory()->for($category)->create(['created_at' => '2024-03-04']);
        $blogD->syncTags([$tagB->name]);

        // act
        $response = $this->getJson(route('public.blogs.show', $blogA));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $blogA->hashid, 'title' => $blogA->title]);
        $this->assertEquals(3, count($response->json('other_blogs')));
        $this->assertEquals($blogC->hashid, $response->json('other_blogs.0.id'));
        $this->assertEquals($blogB->hashid, $response->json('other_blogs.1.id'));
        $this->assertEquals($blogD->hashid, $response->json('other_blogs.2.id'));
    }

    /**
     * Test the admin can get a blog by ID.
     */
    public function test_the_admin_can_get_a_blog_by_id_localized(): void
    {
        // set up
        $this->signInAdmin();
        $blog = Blog::factory()->create();

        // act Thai
        $response = $this->getJson(route('blogs.show', $blog), ['X-Localization' => 'th']);

        // assert Thai
        $response->assertOk();
        $response->assertJsonFragment(['id' => $blog->hashid]);
        $response->assertJsonFragment(['title' => $blog->getTranslation('title', 'th')]);

        // act English
        $response = $this->getJson(route('blogs.show', $blog), ['X-Localization' => 'en']);

        // assert English
        $response->assertOk();
        $response->assertJsonFragment(['id' => $blog->hashid]);
        $response->assertJsonFragment(['title' => $blog->getTranslation('title', 'en')]);
    }

    /**
     * Test the admin can get a blog by ID with all translations.
     */
    public function test_the_admin_can_get_a_blog_by_id_with_all_translations(): void
    {
        // set up
        $this->signInAdmin();
        $blog = Blog::factory()->create();

        // act
        $response = $this->getJson(route('blogs.show', ['blog' => $blog, 'include' => 'translations']));

        // assert
        $response->assertOk();
        $response->assertJsonFragment(['id' => $blog->hashid]);
        $response->assertJsonFragment(['title' => $blog->title]);
        $response->assertJsonFragment(['title' => $blog->getTranslation('title', 'th')]);
        $response->assertJsonFragment(['title' => $blog->getTranslation('title', 'en')]);
        $response->assertJsonStructure(['data' => ['translations' => ['th' => ['title']]]]);
        $response->assertJsonStructure(['data' => ['translations' => ['en' => ['title']]]]);
    }

    /**
     * Test the admin can update a blog.
     */
    public function test_the_admin_can_update_a_blog(): void
    {
        // set up
        $this->signInAdmin();
        $category = Category::factory()->blog()->create();
        $blog = Blog::factory()->create();
        $updatedData = Blog::factory()->make()->toArray();

        // act
        $response = $this->putJson(route('blogs.update', $blog), [
            'category_id' => $category->hashid,
            'slug' => $updatedData['slug'],
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => 'ชื่อบทความ',
                'body' => 'เนื้อหาบทความ',
                'meta_title' => 'ชื่อบทความ',
                'meta_description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'body' => 'Blog content',
                'meta_title' => 'Blog name',
                'meta_description' => 'Blog content',
            ],
        ]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('blogs', ['slug' => $updatedData['slug']]);
        $this->assertDatabaseHas('blog_translations', ['item_id' => $blog->id, 'locale' => 'th', 'title' => 'ชื่อบทความ']);
        $this->assertDatabaseHas('blog_translations', ['item_id' => $blog->id, 'locale' => 'en', 'title' => 'Blog name']);
        $response->assertJsonFragment(['status' => BlogStatus::DRAFT]);
    }

    public function test_the_admin_can_update_a_blog_with_images(): void
    {
        // set up
        $this->signInAdmin();
        $category = Category::factory()->blog()->create();
        $blog = Blog::factory()->create();
        $updatedData = Blog::factory()->make()->toArray();

        [$cover, $thumbnail] = $this->setupImages();

        $existedMediaA = Media::factory()->for(
            $blog,
            'model'
        )->create([
            'file_name' => 'existed_cover_file.png',
            'collection_name' => Blog::MEDIA_COLLECTION_COVER,
        ]);

        // act
        $response = $this->putJson(route('blogs.update', $blog), [
            'category_id' => $category->hashid,
            'slug' => $updatedData['slug'],
            'published_at' => now()->addMonth(),
            'th' => [
                'title' => 'ชื่อบทความ',
                'body' => 'เนื้อหาบทความ',
                'meta_title' => 'ชื่อบทความ',
                'meta_description' => 'เนื้อหาบทความ',
            ],
            'en' => [
                'title' => 'Blog name',
                'body' => 'Blog content',
                'meta_title' => 'Blog name',
                'meta_description' => 'Blog content',
            ],
            'cover' => [
                'id' => $existedMediaA->hashid,
            ],
            'thumbnail' => [
                'id' => null,
                'path' => $thumbnail['path'],
            ],
        ]);

        // assert
        $response->assertOk();
        $this->assertDatabaseHas('blogs', ['slug' => $updatedData['slug']]);
        $this->assertDatabaseHas('blog_translations', ['item_id' => $blog->id, 'locale' => 'th', 'title' => 'ชื่อบทความ']);
        $this->assertDatabaseHas('blog_translations', ['item_id' => $blog->id, 'locale' => 'en', 'title' => 'Blog name']);
        $response->assertJsonFragment(['status' => BlogStatus::DRAFT]);

        $this->assertDatabaseCount('blogs', 1);
        $this->assertDatabaseCount('media', 2);
        $this->assertDatabaseHas('media', [
            'id' => $existedMediaA->id,
            'model_id' => $blog->id,
            'model_type' => Blog::class,
            'collection_name' => Blog::MEDIA_COLLECTION_COVER,
            'file_name' => 'existed_cover_file.png',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $blog->id,
            'model_type' => Blog::class,
            'collection_name' => Blog::MEDIA_COLLECTION_THUMBNAIL,
            'file_name' => $thumbnail['name'],
        ]);
    }

    /**
     * Test the admin can delete a blog.
     */
    public function test_the_admin_can_delete_a_blog(): void
    {
        // set up
        $this->signInAdmin();
        $blog = Blog::factory()->create();

        // act
        $response = $this->deleteJson(route('blogs.destroy', $blog));

        // assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }
}
