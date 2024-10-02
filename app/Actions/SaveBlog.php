<?php

namespace App\Actions;

use App\Models\Blog;
use App\Models\Media;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class SaveBlog
{
    protected Blog $blog;

    /**
     * @throws Exception
     */
    public function execute(Blog $blog, array $data): Blog
    {
        DB::beginTransaction();
        try {
            $this->blog = $blog;
            $this->setBasicAttributes($data);
            $usedImageBody = $this->processBodyImages($data);
            $this->setTranslations($data, $usedImageBody);
            $this->blog->save();
            $this->blog->syncTags($data['tags'] ?? []);
            $this->saveMedia($data);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->blog;
    }

    protected function setBasicAttributes(array $data): void
    {
        $this->blog->published_at = $data['published_at'] ?? null;
        $this->blog->slug = $data['slug'] ?? null;
        $this->blog->category_id = $data['category_id'] ?? null;
    }

    protected function processBodyImages(array $data): array
    {
        $imagesBody = $data['body_images'] ?? [];

        return $imagesBody ? $this->saveImageDescription($data, $imagesBody) : [];
    }

    protected function setTranslations(array $data, array $usedImageBody): void
    {
        $supportedLanguages = config('app.supported_locales');

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }

            $body = $data[$lang]['body'] ?? null;
            if ($body) {
                $body = $this->updateDescriptionImageUrl($body, $usedImageBody);
            }

            $this->blog->setTranslation('title', $data[$lang]['title'] ?? null, $lang);
            $this->blog->setTranslation('body', $body ? Purifier::clean($body) : null, $lang);
            $this->blog->setTranslation('meta_title', $data[$lang]['meta_title'] ?? null, $lang);
            $this->blog->setTranslation('meta_description', $data[$lang]['meta_description'] ?? null, $lang);
        }
    }

    protected function saveMedia(array $data): void
    {
        if (! empty($data['thumbnail']) && empty($data['thumbnail']['id'])) {
            $this->saveImage($data['thumbnail'], Blog::MEDIA_COLLECTION_THUMBNAIL);
        }
        if (! empty($data['cover']) && empty($data['cover']['id'])) {
            $this->saveImage($data['cover'], Blog::MEDIA_COLLECTION_COVER);
        }
    }

    protected function saveImageDescription(array $data, array $imagesDescription): array
    {
        $usedDescriptionImages = [];
        $cleanDescriptions = $this->getCleanDescriptions($data);

        foreach ($imagesDescription as $image) {
            if ($this->isImageUsedInDescription($image, $cleanDescriptions)) {
                $usedDescriptionImages[] = $image;
            } else {
                $this->deleteUnusedImage($image);
            }
        }

        return $this->setMediaIdsForUsedImages($usedDescriptionImages);
    }

    protected function getCleanDescriptions(array $data): array
    {
        $supportedLanguages = config('app.supported_locales');
        $cleanDescriptions = [];

        foreach ($supportedLanguages as $language) {
            $cleanDescriptions[] = str_replace('&amp;', '&', $data[$language]['body'] ?? '');
        }

        return $cleanDescriptions;
    }

    protected function isImageUsedInDescription(array $image, array $cleanDescriptions): bool
    {
        foreach ($cleanDescriptions as $cleanDescription) {
            if (Str::contains($cleanDescription, $image['url'])) {
                return true;
            }
        }

        return false;
    }

    protected function deleteUnusedImage(array $image): void
    {
        if (! empty($image['id'])) {
            $this->blog->media()->where('id', $image['id'])->delete();
        } elseif (! empty($image['path'])) {
            (new SaveTemporaryMedia)->delete($image['path']);
        }
    }

    protected function setMediaIdsForUsedImages(array $usedDescriptionImages): array
    {
        foreach ($usedDescriptionImages as $index => $imageItem) {
            if (empty($imageItem['id']) && ! empty($imageItem['path'])) {
                $media = $this->saveImageFromTempToMedia($imageItem, Blog::MEDIA_COLLECTION_BODY_PHOTO);
                $usedDescriptionImages[$index]['id'] = $media?->id;
            }
        }

        return $usedDescriptionImages;
    }

    protected function updateDescriptionImageUrl(string $description, array $usedDescriptionImages): ?string
    {
        if (! $description) {
            return null;
        }

        foreach ($usedDescriptionImages as $imageItem) {
            if (! empty($imageItem['id']) && ! empty($imageItem['path'])) {
                $media = Media::find($imageItem['id']);
                if ($media) {
                    $tempUrl = str_replace('&', '&amp;', $imageItem['url']);
                    $newUrl = $media->hasGeneratedConversion(Blog::MEDIA_COLLECTION_BODY_PHOTO.'_optimized')
                        ? $media->getFullUrl(Blog::MEDIA_COLLECTION_BODY_PHOTO.'_optimized')
                        : $media->getFullUrl();
                    $description = str_replace($tempUrl, $newUrl, $description);
                }
            }
        }

        return $description;
    }

    protected function saveImage(array $image, string $collection): void
    {
        if (! empty($image) && empty($image['id'])) {
            $this->saveImageFromTempToMedia($image, $collection);
        }
    }

    protected function saveImageFromTempToMedia(array $file, string $collection): ?Media
    {
        return (new SaveTemporaryMedia)->saveFileFromTemp($this->blog, $collection, $file);
    }
}
