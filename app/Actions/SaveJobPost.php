<?php

namespace App\Actions;

use App\Models\JobPost;
use App\Models\Media;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class SaveJobPost
{
    protected JobPost $jobPost;

    /**
     * @throws Exception
     */
    public function execute(JobPost $jobPost, array $data): JobPost
    {
        DB::beginTransaction();
        try {
            $this->jobPost = $jobPost;
            $this->setBasicAttributes($data);
            $usedImageBody = $this->processBodyImages($data);
            $this->setTranslations($data, $usedImageBody);
            $this->jobPost->save();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->jobPost;
    }

    protected function setBasicAttributes(array $data): void
    {
        $this->jobPost->published_at = $data['published_at'] ?? null;
        $this->jobPost->location_id = $data['location_id'] ?? null;
        $this->jobPost->department_id = $data['department_id'] ?? null;
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

            $this->jobPost->setTranslation('title', $data[$lang]['title'] ?? null, $lang);
            $this->jobPost->setTranslation('body', $body ? Purifier::clean($body) : null, $lang);
            $this->jobPost->setTranslation('meta_title', $data[$lang]['meta_title'] ?? null, $lang);
            $this->jobPost->setTranslation('meta_description', $data[$lang]['meta_description'] ?? null, $lang);
        }
    }

    protected function saveImageDescription(array $data, array $bodyImages): array
    {
        $usedDescriptionImages = [];
        $cleanDescriptions = $this->getCleanDescriptions($data);

        foreach ($bodyImages as $image) {
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
            $this->jobPost->media()->where('id', $image['id'])->delete();
        } elseif (! empty($image['path'])) {
            (new SaveTemporaryMedia)->delete($image['path']);
        }
    }

    protected function setMediaIdsForUsedImages(array $usedDescriptionImages): array
    {
        foreach ($usedDescriptionImages as $index => $imageItem) {
            if (empty($imageItem['id']) && ! empty($imageItem['path'])) {
                $media = $this->saveImageFromTempToMedia($imageItem, JobPost::MEDIA_COLLECTION_BODY_PHOTO);
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
            if (! empty($imageItem['id']) && ! empty($imageItem['path'])) { // if temporary media
                $media = Media::find($imageItem['id']);
                if ($media) {
                    $tempUrl = str_replace('&', '&amp;', $imageItem['url']);
                    $newUrl = $media->hasGeneratedConversion(JobPost::MEDIA_COLLECTION_BODY_PHOTO.'_optimized')
                        ? $media->getFullUrl(JobPost::MEDIA_COLLECTION_BODY_PHOTO.'_optimized')
                        : $media->getFullUrl();
                    $description = str_replace($tempUrl, $newUrl, $description);
                }
            }
        }

        return $description;
    }

    protected function saveImageFromTempToMedia(array $file, string $collection): ?Media
    {
        if (! empty($file) && empty($file['id'])) {
            return (new SaveTemporaryMedia)->saveFileFromTemp($this->jobPost, $collection, $file);
        }

        return null;
    }
}
