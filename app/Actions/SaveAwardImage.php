<?php

namespace App\Actions;

use App\Models\AwardImage;
use Exception;
use Illuminate\Support\Facades\DB;

class SaveAwardImage
{
    protected AwardImage $awardImage;

    /**
     * @throws Exception
     */
    public function execute(AwardImage $awardImage, array $data): AwardImage
    {
        DB::beginTransaction();
        try {
            $this->awardImage = $awardImage;
            if (! $this->awardImage->id) {
                $lastOrder = AwardImage::orderBy('order', 'desc')->first()->order ?? 0;
                $this->awardImage->order = $lastOrder == 0 ? 0 : $lastOrder + 1;
            }
            $this->awardImage->save();
            $this->saveMedia($data);

            DB::commit();

            return $this->awardImage;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    protected function saveMedia(array $data): void
    {
        $this->awardImage->addMedia($data['image'])
            ->toMediaCollection(AwardImage::MEDIA_COLLECTION_IMAGE);
    }
}
