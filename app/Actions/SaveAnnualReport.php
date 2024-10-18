<?php

namespace App\Actions;

use App\Models\AnnualReport;
use App\Models\Media;
use Exception;
use Illuminate\Support\Facades\DB;

class SaveAnnualReport
{
    protected AnnualReport $annualReport;

    /**
     * @throws Exception
     */
    public function execute(AnnualReport $annualReport, array $data): AnnualReport
    {
        DB::beginTransaction();
        try {
            $this->annualReport = $annualReport;
            $this->setBasicAttributes($data);
            $this->setTranslations($data);
            $this->annualReport->save();
            $this->saveMedia($data);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->annualReport;
    }

    protected function setBasicAttributes(array $data): void
    {
        $this->annualReport->published_at = $data['published_at'] ?? null;
        if (! $this->annualReport->id) {
            $lastOrder = AnnualReport::orderBy('order', 'desc')->first()->order ?? 0;
            $this->annualReport->order = $lastOrder == 0 ? 0 : $lastOrder + 1;
        }
    }

    protected function setTranslations(array $data): void
    {
        $supportedLanguages = config('app.supported_locales');

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }
            $this->annualReport->setTranslation('title', $data[$lang]['title'] ?? null, $lang);
        }
    }

    protected function saveMedia(array $data): void
    {
        if (! empty($data['file']) && empty($data['file']['id'])) {
            $this->saveImageFromTempToMedia($data['file'], AnnualReport::MEDIA_COLLECTION_FILE);
        }
        if (! empty($data['cover']) && empty($data['cover']['id'])) {
            $this->saveImageFromTempToMedia($data['cover'], AnnualReport::MEDIA_COLLECTION_COVER);
        }
        if (empty($data['cover'])) {
            $this->deleteMedia(AnnualReport::MEDIA_COLLECTION_COVER);
        }
        if (empty($data['file'])) {
            $this->deleteMedia(AnnualReport::MEDIA_COLLECTION_FILE);
        }
    }

    protected function saveImageFromTempToMedia(array $file, string $collection): ?Media
    {
        if (! empty($file) && empty($file['id'])) {
            return (new SaveTemporaryMedia)->saveFileFromTemp($this->annualReport, $collection, $file);
        }

        return null;
    }

    protected function deleteMedia(string $collectionName)
    {
        try {
            $image = $this->annualReport->getFirstMedia($collectionName);
            if ($image) {
                $this->annualReport->deleteMedia($image->id);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
