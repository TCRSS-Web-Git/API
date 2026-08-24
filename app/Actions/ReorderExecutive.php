<?php

namespace App\Actions;

use App\Models\Executive;
use Exception;
use Illuminate\Support\Facades\DB;

class ReorderExecutive
{
    /**
     * Reassign group_order (tier) and order (within tier) from the given hierarchy.
     *
     * @throws Exception
     */
    public function execute(array $data): void
    {
        DB::beginTransaction();
        try {
            $groupOrder = 0;
            foreach ($data['groups'] as $group) {
                $ids = $group['ids'] ?? [];
                if (empty($ids)) {
                    continue; // skip empty groups so tiers stay contiguous
                }

                foreach ($ids as $order => $id) {
                    Executive::where('id', $id)->update([
                        'group_order' => $groupOrder,
                        'order' => $order,
                    ]);
                }

                $groupOrder++;
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
