<?php

namespace App\Actions;

use App\Models\Popup;
use Exception;
use Illuminate\Support\Facades\DB;

class SavePopup
{
    protected Popup $popup;

    /**
     * @throws Exception
     */
    public function execute(Popup $popup, array $data): Popup
    {
        DB::beginTransaction();
        try {
            $this->popup = $popup;
            if (! $this->popup->id) {
                $lastOrder = Popup::orderBy('order', 'desc')->first()->order ?? 0;
                $this->popup->order = $lastOrder == 0 ? 0 : $lastOrder + 1;
            }

            $this->popup->is_active = $data['is_active'] ?? false;
            $this->popup->save();
            if (isset($data['image'])) {
                $this->saveMedia($data);
            }

            DB::commit();
            return $this->popup;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    protected function saveMedia(array $data): void
    {
        $this->popup->addMedia($data['image'])
            ->toMediaCollection(Popup::MEDIA_COLLECTION_IMAGE);
    }
}
