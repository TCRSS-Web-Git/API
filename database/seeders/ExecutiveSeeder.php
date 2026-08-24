<?php

namespace Database\Seeders;

use App\Models\Executive;
use Illuminate\Database\Seeder;

class ExecutiveSeeder extends Seeder
{
    /**
     * Seed the executives (ผู้บริหารระดับสูง).
     *
     * Data mirrors the current public page, arranged into hierarchy tiers (group_order):
     *   0 = President, 1 = Executive Vice Presidents, 2 = Vice Presidents, 3 = General Managers.
     */
    public function run(): void
    {
        // Idempotent: skip if already seeded.
        if (Executive::query()->exists()) {
            return;
        }

        foreach ($this->data() as $row) {
            $executive = new Executive;
            $executive->group_order = $row['group_order'];
            $executive->order = $row['order'];
            $executive->published_at = now();
            $executive->save();

            $executive->setTranslation('name', $row['name_th'], 'th');
            $executive->setTranslation('name', $row['name_en'], 'en');
            $executive->setTranslation('position', $row['position_th'], 'th');
            $executive->setTranslation('position', $row['position_en'], 'en');
            $executive->save();

            $path = storage_path('data/members/'.$row['image']);
            if (is_file($path)) {
                $executive->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection(Executive::MEDIA_COLLECTION_IMAGE);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function data(): array
    {
        return [
            // Group 0 — President
            [
                'group_order' => 0, 'order' => 0,
                'name_th' => 'นายยงยุทธ มลิทอง',
                'name_en' => 'Mr. Yongyuth Malithong',
                'position_th' => 'กรรมการผู้จัดการใหญ่',
                'position_en' => 'President',
                'image' => 'board-of-director/Yongyuth-Malithong.png',
            ],

            // Group 1 — Executive Vice Presidents
            [
                'group_order' => 1, 'order' => 0,
                'name_th' => 'นายยูสุเกะ คาวาโมโตะ',
                'name_en' => 'Mr. Yusuke Kawamoto',
                'position_th' => 'รองกรรมการผู้จัดการใหญ่ สายการขายและการตลาด',
                'position_en' => 'Executive Vice President of Sales and Marketing Division',
                'image' => 'executive-management/Yusuke-Kawamoto.png',
            ],
            [
                'group_order' => 1, 'order' => 1,
                'name_th' => 'นายมนินทร์ อินทร์พรหม',
                'name_en' => 'Mr. Manin Inprom',
                'position_th' => 'รองกรรมการผู้จัดการใหญ่ สายบริหารทั่วไป',
                'position_en' => 'Executive Vice President of General Administration Division',
                'image' => 'executive-management/Manin-Inprom-2.png',
            ],
            [
                'group_order' => 1, 'order' => 2,
                'name_th' => 'นายฮิโรชิ นากาโนะ',
                'name_en' => 'Mr. Hiroshi Nakano',
                'position_th' => "รองกรรมการผู้จัดการใหญ่ สายการเงินและบัญชี\n และรองกรรมการผู้จัดการใหญ่ สายจัดหาวัตถุดิบ",
                'position_en' => 'Executive Vice President of Finance and Accounting Division and Executive Vice President of HRC Procurement Division',
                'image' => 'board-of-director/Hiroshi-Nakano.png',
            ],
            [
                'group_order' => 1, 'order' => 3,
                'name_th' => 'นายมานพ ยอดเอี่ยม',
                'name_en' => 'Mr. Manop Yodeiam',
                'position_th' => 'ผู้ช่วยกรรมการผู้จัดการใหญ่ สายการผลิต',
                'position_en' => 'Vice President of Manufacturing Division',
                'image' => 'executive-management/Manop-Yodeiam.png',
            ],
            [
                'group_order' => 1, 'order' => 4,
                'name_th' => 'นายพรเทพ หงษ์ดิลกกุล',
                'name_en' => 'Mr. Pornthep Hongdilokkul',
                'position_th' => 'ผู้ช่วยกรรมการผู้จัดการใหญ่ สายการขายและการตลาด',
                'position_en' => 'Vice President of Sales and Marketing Division',
                'image' => 'executive-management/Pornthep-Hongdilokkul-2.png',
            ],
            [
                'group_order' => 1, 'order' => 5,
                'name_th' => 'นายรวิ คำดวงดาว',
                'name_en' => 'Mr. Rawi Kumduongdaow',
                'position_th' => 'ผู้ช่วยกรรมการผู้จัดการใหญ่ สายการเงินและบัญชี',
                'position_en' => 'Vice President of Finance and Accounting Division',
                'image' => 'executive-management/Rawi-Kumduongdaow.png',
            ],
            [
                'group_order' => 1, 'order' => 6,
                'name_th' => 'นายฮิเดฮิโระ อิโซเบะ',
                'name_en' => 'Mr. Hidehiro Isobe',
                'position_th' => 'ผู้จัดการทั่วไป สายการขายและการตลาด',
                'position_en' => 'General Manager of Sales and Marketing Division',
                'image' => 'executive-management/Hidehiro-Isobe.png',
            ],
            [
                'group_order' => 1, 'order' => 7,
                'name_th' => 'นายโทโมะยูกิ ชิมิสึ',
                'name_en' => 'Mr. Tomoyuki Shimizu',
                'position_th' => 'ผู้จัดการทั่วไป สายการผลิต',
                'position_en' => 'General Manager of Manufacturing Division',
                'image' => 'executive-management/Tomoyuki-Shimizu.png',
            ],
        ];
    }
}
