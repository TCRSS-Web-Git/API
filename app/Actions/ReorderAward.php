<?php

namespace App\Actions;

use App\Models\Award;
use Exception;
use Illuminate\Support\Facades\DB;

class ReorderAward
{
    /**
     * @throws Exception
     */
    public function execute(array $data): void
    {
        DB::beginTransaction();
        try {
            foreach ($data['ids'] as $order => $id) {
                Award::where('id', $id)->update(['order' => $order]);
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
