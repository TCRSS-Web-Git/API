<?php

namespace Database\Seeders;

use App\Models\BoardDirector;
use Illuminate\Database\Seeder;

class BoardDirectorSeeder extends Seeder
{
    /**
     * Seed the board of directors (คณะกรรมการบริษัท).
     *
     * Data mirrors the current public page, arranged into hierarchy tiers (group_order):
     *   0 = Chairman, 1 = Director and President, 2 = Directors.
     */
    public function run(): void
    {
        // Idempotent: skip if already seeded.
        if (BoardDirector::query()->exists()) {
            return;
        }

        foreach ($this->data() as $row) {
            $director = new BoardDirector;
            $director->group_order = $row['group_order'];
            $director->order = $row['order'];
            $director->published_at = now();
            $director->save();

            $director->setTranslation('name', $row['name_th'], 'th');
            $director->setTranslation('name', $row['name_en'], 'en');
            $director->setTranslation('position', $row['position_th'], 'th');
            $director->setTranslation('position', $row['position_en'], 'en');
            $director->save();

            $path = storage_path('data/members/'.$row['image']);
            if (is_file($path)) {
                $director->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection(BoardDirector::MEDIA_COLLECTION_IMAGE);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function data(): array
    {
        return [
            // Group 0 — Chairman
            [
                'group_order' => 0, 'order' => 0,
                'name_th' => 'นายฮิซาโนริ เอโนกิ',
                'name_en' => 'Mr. Hisanori Enoki',
                'position_th' => 'ประธานกรรมการ',
                'position_en' => 'Chairman',
                'image' => 'board-of-director/Hisanori-Enoki.png',
            ],

            // Group 1 — Director and President
            [
                'group_order' => 1, 'order' => 0,
                'name_th' => 'นายยงยุทธ มลิทอง',
                'name_en' => 'Mr. Yongyuth Malithong',
                'position_th' => 'กรรมการและกรรมการผู้จัดการใหญ่',
                'position_en' => 'Director and President',
                'image' => 'board-of-director/Yongyuth-Malithong.png',
            ],
            [
                'group_order' => 1, 'order' => 1,
                'name_th' => 'นายณรงค์ฤทธิ์ โชตินุชิตตระกูล',
                'name_en' => 'Mr. Narongrit Chotnuchittakul',
                'position_th' => 'กรรมการ',
                'position_en' => 'Director',
                'image' => 'board-of-director/Narongrit-Chotnuchittakul.png',
            ],
            [
                'group_order' => 1, 'order' => 2,
                'name_th' => 'นายโทชิอากิ ฟูจินากะ',
                'name_en' => 'Mr. Toshiaki Fujinaka',
                'position_th' => 'กรรมการ',
                'position_en' => 'Director',
                'image' => 'board-of-director/Toshiaki-Fujinaka.png',
            ],
            [
                'group_order' => 1, 'order' => 3,
                'name_th' => 'นายชินอิจิ ยะกุจิ',
                'name_en' => 'Mr. Shinichi Yaguchi',
                'position_th' => 'กรรมการ',
                'position_en' => 'Director',
                'image' => 'board-of-director/Shinichi-Yaguchi.png',
            ],
            [
                'group_order' => 1, 'order' => 4,
                'name_th' => 'นายโนบุโยชิ คาริยะ',
                'name_en' => 'Mr. Nobuyoshi Kariya',
                'position_th' => 'กรรมการ',
                'position_en' => 'Director',
                'image' => 'board-of-director/Nobuyoshi-Kariya.png',
            ],
            [
                'group_order' => 1, 'order' => 5,
                'name_th' => 'นายฮิโรชิ นากาโนะ',
                'name_en' => 'Mr. Hiroshi Nakano',
                'position_th' => 'กรรมการ',
                'position_en' => 'Director',
                'image' => 'board-of-director/Hiroshi-Nakano.png',
            ],
            [
                'group_order' => 1, 'order' => 6,
                'name_th' => 'นายเฉลิม อังกาทิพย์',
                'name_en' => 'Mr. Chalerm Angkatip',
                'position_th' => 'กรรมการ',
                'position_en' => 'Director',
                'image' => 'board-of-director/Chalerm-Angatip.png',
            ],
        ];
    }
}
