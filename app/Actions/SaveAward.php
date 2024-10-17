<?php

namespace App\Actions;

use App\Models\Award;
use App\Models\Media;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class SaveAward
{
    protected Award $award;

    /**
     * @throws Exception
     */
    public function execute(Award $award, array $data): Award
    {
        DB::beginTransaction();
        try {
            $this->award = $award;
            $this->setBasicAttributes($data);
            $usedDescriptionImages = $this->processDescriptionImages($data);
            $this->setTranslations($data, $usedDescriptionImages);
            $this->award->save();
            DB::commit();

            return $this->award;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    protected function setBasicAttributes(array $data): void
    {
        $this->award->published_at = $data['published_at'] ?? null;
        if (! $this->award->id) {
            $lastOrder = Award::orderBy('order', 'desc')->first();
            $this->award->order = ($lastOrder->order ?? -1) + 1;
        }
    }

    protected function setTranslations(array $data, array $usedDescriptionImages): void
    {
        $supportedLanguages = config('app.supported_locales');

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }

            $description = $data[$lang]['description'] ?? null;
            if ($description) {
                $description = $this->updateDescriptionImageUrl($description, $usedDescriptionImages);
            }

            $this->award->setTranslation('title', $data[$lang]['title'] ?? null, $lang);
            $this->award->setTranslation('description', $description ? Purifier::clean($description) : null, $lang);
        }
    }

    protected function processDescriptionImages(array $data): array
    {
        $descriptionImages = $data['description_images'] ?? [];

        return $descriptionImages ? $this->saveDescriptionImages($data, $descriptionImages) : [];
    }

    protected function saveDescriptionImages(array $data, array $descriptionImages): array
    {
        $usedDescriptionImages = [];
        $cleanDescriptions = $this->getCleanDescriptions($data);

        foreach ($descriptionImages as $image) {
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
            $cleanDescriptions[] = str_replace('&amp;', '&', $data[$language]['description'] ?? '');
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
            $this->award->media()->where('id', $image['id'])->delete();
        } elseif (! empty($image['path'])) {
            (new SaveTemporaryMedia)->delete($image['path']);
        }
    }

    protected function setMediaIdsForUsedImages(array $usedDescriptionImages): array
    {
        foreach ($usedDescriptionImages as $index => $imageItem) {
            if (empty($imageItem['id']) && ! empty($imageItem['path'])) {
                $media = $this->saveImageFromTempToMedia($imageItem, Award::MEDIA_COLLECTION_DESCRIPTION_PHOTO);
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
                    $newUrl = $media->hasGeneratedConversion(Award::MEDIA_COLLECTION_DESCRIPTION_PHOTO.'_optimized')
                        ? $media->getFullUrl(Award::MEDIA_COLLECTION_DESCRIPTION_PHOTO.'_optimized')
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
            return (new SaveTemporaryMedia)->saveFileFromTemp($this->award, $collection, $file);
        }

        return null;
    }
}
