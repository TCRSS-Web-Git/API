<?php

namespace App\Actions;

use App\Models\BoardDirector;
use App\Models\Media;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveBoardDirector
{
    protected BoardDirector $boardDirector;

    /**
     * @throws Exception
     */
    public function execute(BoardDirector $boardDirector, array $data): BoardDirector
    {
        DB::beginTransaction();
        try {
            $this->boardDirector = $boardDirector;
            $this->setBasicAttributes();
            $this->boardDirector->updated_by = Auth::id();
            
            $this->boardDirector->published_at = $data['published_at'] ?? null;
            $this->boardDirector->published_by = $data['published_at'] ? Auth::id() : null;

            $this->setTranslations($data);
            $this->boardDirector->save();
            $this->saveMedia($data);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->boardDirector;
    }

    protected function setBasicAttributes(): void
    {
        if (! $this->boardDirector->id) {
            // Append new record to the end of the last (bottom) group; admin can drag it elsewhere afterwards.
            $last = BoardDirector::orderByDesc('group_order')->orderByDesc('order')->first();
            $this->boardDirector->group_order = $last->group_order ?? 0;
            $this->boardDirector->order = $last ? $last->order + 1 : 0;
            $this->boardDirector->created_by = Auth::id();
        }
    }

    protected function setTranslations(array $data): void
    {
        $supportedLanguages = config('app.supported_locales');

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }
            $this->boardDirector->setTranslation('name', $data[$lang]['name'] ?? null, $lang);
            $this->boardDirector->setTranslation('position', $data[$lang]['position'] ?? null, $lang);
        }
    }

    protected function saveMedia(array $data): void
    {
        if (! empty($data['image']) && empty($data['image']['id'])) {
            $this->saveImageFromTempToMedia($data['image'], BoardDirector::MEDIA_COLLECTION_IMAGE);
        }
        if (empty($data['image'])) {
            $this->deleteMedia(BoardDirector::MEDIA_COLLECTION_IMAGE);
        }
    }

    protected function saveImageFromTempToMedia(array $file, string $collection): ?Media
    {
        if (! empty($file) && empty($file['id'])) {
            return (new SaveTemporaryMedia)->saveFileFromTemp($this->boardDirector, $collection, $file);
        }

        return null;
    }

    protected function deleteMedia(string $collectionName): void
    {
        $image = $this->boardDirector->getFirstMedia($collectionName);
        if ($image) {
            $this->boardDirector->deleteMedia($image->id);
        }
    }
}
