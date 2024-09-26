<?php

namespace App\Actions;

use App\Models\Blog;
use App\Models\Media;
use Exception;
use Mews\Purifier\Facades\Purifier;

class SaveBlog
{
    protected Blog $blog;

    /**
     * @throws Exception
     */
    public function execute(Blog $blog, array $data): Blog
    {
        $supportedLanguages = config('app.supported_locales');

        $blog->published_at = $data['published_at'] ?? null;
        $blog->slug = $data['slug'] ?? null;
        $blog->category_id = $data['category_id'] ?? null;

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }

            $blog->setTranslation('title', $data[$lang]['title'] ?? null, $lang);
            $blog->setTranslation('body', isset($data[$lang]['body']) ? Purifier::clean($data[$lang]['body']) : null, $lang);
            $blog->setTranslation('meta_title', $data[$lang]['meta_title'] ?? null, $lang);
            $blog->setTranslation('meta_description', $data[$lang]['meta_description'] ?? null, $lang);
        }

        $blog->save();

        $this->blog = $blog;

        //save images
        // check if data have media_thumbnail and media_cover
        if (array_key_exists('media_thumbnail', $data) && $data['media_thumbnail']) {
            $this->saveImage($data['media_thumbnail'], Blog::MEDIA_COLLECTION_THUMBNAIL);
        }
        if (array_key_exists('media_cover', $data) && $data['media_cover']) {
            $this->saveImage($data['media_cover'], Blog::MEDIA_COLLECTION_COVER);
        }

        return $this->blog;
    }

    // TODO refactor this method
    protected function saveImage(array $image, string $collection): void
    {
        if ($image && ! $image['id']) {
            $this->saveImageFromTempToMedia($image, $collection);
        }
    }

    protected function saveImageFromTempToMedia($file, $collection): ?Media
    {
        return (new SaveTemporaryMedia)->saveFileFromTemp($this->blog, $collection, $file);
    }
}
