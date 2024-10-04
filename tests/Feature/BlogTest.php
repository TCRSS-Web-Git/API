<?php

namespace Tests\Feature;

use App\Enums\BlogStatus;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

        $fileCover = UploadedFile::fake()->image('image_cover.jpg');
        $fileThumbnail = UploadedFile::fake()->image('image_thumbnail.jpg');
        $mediaImageCover = $this->postJson(route('temporary_media.store'), ['media' => $fileCover]);
        $mediaImageThumbnail = $this->postJson(route('temporary_media.store'), ['media' => $fileThumbnail]);

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
                'path' => $mediaImageCover->json('data')['path'],
            ],
            'thumbnail' => [
                'id' => null,
                'path' => $mediaImageThumbnail->json('data')['path'],
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
        $this->assertDatabaseHas('media', [
            'model_type' => Blog::class,
            'collection_name' => Blog::MEDIA_COLLECTION_COVER,
            'file_name' => 'image_cover.jpg',
        ]);
        $this->assertDatabaseHas('media', [
            'model_type' => Blog::class,
            'collection_name' => Blog::MEDIA_COLLECTION_THUMBNAIL,
            'file_name' => 'image_thumbnail.jpg',
        ]);
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
