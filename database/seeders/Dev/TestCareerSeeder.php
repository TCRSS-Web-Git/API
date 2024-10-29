<?php

namespace Database\Seeders\Dev;

use App\Enums\CategoryType;
use App\Models\Career;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TestCareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careerTypes = [
            'Day time',
            'พนักงานประจำสำนักงาน',
            'Shift Work',
            'พนักงานกะ',
            'Temporary',
            'พนักงานชั่วคราว',
            'Trainee',
            'นักศึกษาฝึกงาน',
        ];
        for ($i = 0; $i < count($careerTypes); $i = $i + 2) {
            $careerType = Category::create(['type' => CategoryType::CAREER_TYPE]);
            $careerType->setTranslation('name', $careerTypes[$i], 'en');
            $careerType->setTranslation('name', $careerTypes[$i + 1], 'th');
            $careerType->save();
        }

        $departments = [
            'Sales - Japanese Companies Department',
            'ฝ่ายขาย - บริษัทญี่ปุ่น',
            'Sales - Thai Companies Department',
            'ฝ่ายขาย - บริษัทไทย',
            'Sales Coordination Department',
            'ฝ่ายประสานงานการขาย',
            'Technical Service Department',
            'ฝ่ายบริการเทคนิค',
            'Finance Department',
            'ฝ่ายการเงิน',
            'Managerial Accounting Department',
            'ฝ่ายบัญชีบริหาร',
            'General Accounting Department',
            'ฝ่ายบัญชีทั่วไป',
            'Budgeting & Cost Accounting Department',
            'ฝ่ายงบประมาณและบัญชีต้นทุน',
            'Procurement Department',
            'ฝ่ายจัดหา',
            'Information Technology Department',
            'ฝ่ายเทคโนโลยีสารสนเทศ',
            'HR & Administration Department',
            'ฝ่ายทรัพยากรบุคคลและธุรการ',
            'Government Relation & Legal Department',
            'ฝ่ายรัฐกิจสัมพันธ์และกฎหมาย',
            'HRC Procurement Department',
            'ฝ่ายจัดหาวัตถุดิบ',
            'Internal Audit Office Department',
            'ฝ่ายสำนักตรวจสอบภายใน',
            'Operation Department',
            'ฝ่ายปฏิบัติการผลิต',
            'Planning and Logistics Department',
            'ฝ่ายวางแผนการผลิตและการจัดการขนส่ง',
            'Quality Assurance Department',
            'ฝ่ายประกันคุณภาพ',
            'Technical Quality Control Department',
            'ฝ่ายควบคุมเทคนิคคุณภาพ',
            'Mechanical Maintenance Department',
            'ฝ่ายซ่อมบำรุงเครื่องกล',
            'Electrical Maintenance Department',
            'ฝ่ายซ่อมบำรุงไฟฟ้า',
            'FSED Department',
            'ฝ่ายบริหารความปลอดภัยและสิ่งแวดล้อมโรงงาน',
            'Other',
            'อื่นๆ',
        ];
        for ($i = 0; $i < count($departments); $i = $i + 2) {
            $department = Category::create(['type' => CategoryType::DEPARTMENT]);
            $department->setTranslation('name', $departments[$i], 'en');
            $department->setTranslation('name', $departments[$i + 1], 'th');
            $department->save();
        }

        $locations = [
            'Head office (Bangkok)',
            'สำนักงานใหญ่ (กทม.)',
            'Plant office (Prachuap Khiri Khan)',
            'โรงงานบางสะพาน (อ.บางสะพาน จ.ประจวบคีรีขันธ์)',
        ];
        for ($i = 0; $i < count($locations); $i = $i + 2) {
            $location = Category::create(['type' => CategoryType::LOCATION]);
            $location->setTranslation('name', $locations[$i], 'en');
            $location->setTranslation('name', $locations[$i + 1], 'th');
            $location->save();
        }

        $careerTypeIds = Category::where('type', CategoryType::CAREER_TYPE)->pluck('id');
        $departmentIds = Category::where('type', CategoryType::DEPARTMENT)->pluck('id');
        $locationIds = Category::where('type', CategoryType::LOCATION)->pluck('id');

        Career::factory(10)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'type_id' => fake()->randomElement($careerTypeIds),
                    'department_id' => fake()->randomElement($departmentIds),
                    'location_id' => fake()->randomElement($locationIds),
                ],
            ))
            ->draft()
            ->create();

        Career::factory(20)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'type_id' => fake()->randomElement($careerTypeIds),
                    'department_id' => fake()->randomElement($departmentIds),
                    'location_id' => fake()->randomElement($locationIds),
                ],
            ))
            ->published()
            ->create();
    }
}
