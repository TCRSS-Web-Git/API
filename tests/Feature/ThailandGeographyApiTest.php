<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\Region;
use App\Models\Subdistrict;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThailandGeographyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_districts_api(): void
    {
        $this->setupGeo();

        $response = $this->get(route('geography.districts', $this->province->hashid));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->amphoe1->hashid]);
        $response->assertJsonFragment(['id' => $this->amphoe2->hashid]);
    }

    protected function setupGeo()
    {
        Model::unguard();

        $region = Region::create(['name_th' => 'ภาคกลาง', 'name_en' => 'Central']);
        $this->province = Province::create(['region_id' => $region->id, 'name_th' => 'กรุงเทพมหานคร', 'name_en' => 'Bangkok', 'iso3166_2' => 'TH-10']);
        $this->amphoe1 = District::create(['province_id' => $this->province->id, 'name_th' => 'ราชเทวี', 'name_en' => 'Ratchathewi']);
        $this->amphoe2 = District::create(['province_id' => $this->province->id, 'name_th' => 'ดุสิต', 'name_en' => 'Dusit']);

        $this->tambon11 = Subdistrict::create(['district_id' => $this->amphoe1->id, 'name_th' => 'ทุ่งพญาไท', 'name_en' => 'Thung Phaya Thai', 'zip' => '10400']);
        $this->tambon12 = Subdistrict::create(['district_id' => $this->amphoe1->id, 'name_th' => 'ถนนพญาไท', 'name_en' => 'Thanon Phaya Thai', 'zip' => '10400']);
        $this->tambon13 = Subdistrict::create(['district_id' => $this->amphoe1->id, 'name_th' => 'ถนนเพชรบุรี', 'name_en' => 'Thanon Phetchaburi', 'zip' => '10400']);
        $this->tambon14 = Subdistrict::create(['district_id' => $this->amphoe1->id, 'name_th' => 'มักกะสัน', 'name_en' => 'Makkasan', 'zip' => '10400']);

        $this->tambon21 = Subdistrict::create(['district_id' => $this->amphoe2->id, 'name_th' => 'ดุสิต', 'name_en' => 'Dusit', 'zip' => '10300']);
        $this->tambon22 = Subdistrict::create(['district_id' => $this->amphoe2->id, 'name_th' => 'วชิรพยาบาล', 'name_en' => 'Wachiraphayaban', 'zip' => '10300']);
        $this->tambon23 = Subdistrict::create(['district_id' => $this->amphoe2->id, 'name_th' => 'สี่แยกมหานาค', 'name_en' => 'Si Yaek Maha Nak', 'zip' => '10300']);

        Model::reguard();
    }

    public function test_subdistricts_api(): void
    {
        $this->setupGeo();

        $response = $this->get(route('geography.subdistricts', $this->amphoe2->hashid));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->tambon21->hashid]);
        $response->assertJsonFragment(['id' => $this->tambon22->hashid]);
        $response->assertJsonFragment(['id' => $this->tambon23->hashid]);
    }

    public function test_provices_api(): void
    {
        $this->setupGeo();
        $response = $this->get(route('geography.provinces'));
        $response->assertJsonFragment(['id' => $this->province->hashid, 'name_th' => 'กรุงเทพมหานคร']);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }
}
