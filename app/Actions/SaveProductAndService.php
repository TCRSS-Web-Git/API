<?php

namespace App\Actions;

use App\Models\Media;
use App\Models\ProductAndService;
use Exception;
use Illuminate\Support\Facades\DB;

class SaveProductAndService
{
    protected ProductAndService $productAndService;

    /**
     * @throws Exception
     */
    public function execute(ProductAndService $productAndService, array $data): ProductAndService
    {
        DB::beginTransaction();
        try {
            $this->productAndService = $productAndService;
            $this->setBasicAttributes($data);
            $this->setTranslations($data);
            $this->productAndService->save();
            $this->saveMedia($data);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->productAndService;
    }

    protected function setBasicAttributes(array $data): void
    {
        $this->productAndService->published_at = $data['published_at'] ?? null;
        if (! $this->productAndService->id) {
            $lastOrder = ProductAndService::orderBy('order', 'desc')->first();
            $this->productAndService->order = ($lastOrder->order ?? -1) + 1;
        }
    }

    protected function setTranslations(array $data): void
    {
        $supportedLanguages = config('app.supported_locales');

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }
            $this->productAndService->setTranslation('title', $data[$lang]['title'] ?? null, $lang);
        }
    }

    protected function saveMedia(array $data): void
    {
        if (! empty($data['file']) && empty($data['file']['id'])) {
            $this->saveImageFromTempToMedia($data['file'], ProductAndService::MEDIA_COLLECTION_FILE);
        }
        if (! empty($data['cover']) && empty($data['cover']['id'])) {
            $this->saveImageFromTempToMedia($data['cover'], ProductAndService::MEDIA_COLLECTION_COVER);
        }
    }

    protected function saveImageFromTempToMedia(array $file, string $collection): ?Media
    {
        if (! empty($file) && empty($file['id'])) {
            return (new SaveTemporaryMedia)->saveFileFromTemp($this->productAndService, $collection, $file);
        }

        return null;
    }
}
