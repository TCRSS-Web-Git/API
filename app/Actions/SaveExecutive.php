<?php

namespace App\Actions;

use App\Models\Executive;
use App\Models\Media;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveExecutive
{
    protected Executive $executive;

    /**
     * @throws Exception
     */
    public function execute(Executive $executive, array $data): Executive
    {
        DB::beginTransaction();
        try {
            $this->executive = $executive;
            $this->setBasicAttributes();
            $this->executive->updated_by = Auth::id();

            $this->executive->published_at = $data['published_at'] ?? null;
            $this->executive->published_by = $data['published_at'] ? Auth::id() : null;

            $this->setTranslations($data);
            $this->executive->save();
            $this->saveMedia($data);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->executive;
    }

    protected function setBasicAttributes(): void
    {
        if (! $this->executive->id) {
            // Append new record to the end of the last (bottom) group; admin can drag it elsewhere afterwards.
            $last = Executive::orderByDesc('group_order')->orderByDesc('order')->first();
            $this->executive->group_order = $last->group_order ?? 0;
            $this->executive->order = $last ? $last->order + 1 : 0;
            $this->executive->created_by = Auth::id();
        }
    }

    protected function setTranslations(array $data): void
    {
        $supportedLanguages = config('app.supported_locales');

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }
            $this->executive->setTranslation('name', $data[$lang]['name'] ?? null, $lang);
            $this->executive->setTranslation('position', $data[$lang]['position'] ?? null, $lang);
        }
    }

    protected function saveMedia(array $data): void
    {
        if (! empty($data['image']) && empty($data['image']['id'])) {
            $this->saveImageFromTempToMedia($data['image'], Executive::MEDIA_COLLECTION_IMAGE);
        }
        if (empty($data['image'])) {
            $this->deleteMedia(Executive::MEDIA_COLLECTION_IMAGE);
        }
    }

    protected function saveImageFromTempToMedia(array $file, string $collection): ?Media
    {
        if (! empty($file) && empty($file['id'])) {
            return (new SaveTemporaryMedia)->saveFileFromTemp($this->executive, $collection, $file);
        }

        return null;
    }

    protected function deleteMedia(string $collectionName): void
    {
        $image = $this->executive->getFirstMedia($collectionName);
        if ($image) {
            $this->executive->deleteMedia($image->id);
        }
    }
}
