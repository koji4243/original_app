<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhkAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['area_code' => '010', 'name' => '札幌'],
            ['area_code' => '011', 'name' => '函館'],
            ['area_code' => '012', 'name' => '旭川'],
            ['area_code' => '013', 'name' => '帯広'],
            ['area_code' => '014', 'name' => '釧路'],
            ['area_code' => '015', 'name' => '北見'],
            ['area_code' => '016', 'name' => '室蘭'],
            ['area_code' => '020', 'name' => '青森'],
            ['area_code' => '030', 'name' => '盛岡'],
            ['area_code' => '040', 'name' => '仙台'],
            ['area_code' => '050', 'name' => '秋田'],
            ['area_code' => '060', 'name' => '山形'],
            ['area_code' => '070', 'name' => '福島'],
            ['area_code' => '080', 'name' => '水戸'],
            ['area_code' => '090', 'name' => '宇都宮'],
            ['area_code' => '100', 'name' => '前橋'],
            ['area_code' => '110', 'name' => 'さいたま'],
            ['area_code' => '120', 'name' => '千葉'],
            ['area_code' => '130', 'name' => '東京'],
            ['area_code' => '140', 'name' => '横浜'],
            ['area_code' => '150', 'name' => '新潟'],
            ['area_code' => '160', 'name' => '富山'],
            ['area_code' => '170', 'name' => '金沢'],
            ['area_code' => '180', 'name' => '福井'],
            ['area_code' => '190', 'name' => '甲府'],
            ['area_code' => '200', 'name' => '長野'],
            ['area_code' => '210', 'name' => '岐阜'],
            ['area_code' => '220', 'name' => '静岡'],
            ['area_code' => '230', 'name' => '名古屋'],
            ['area_code' => '240', 'name' => '津'],
            ['area_code' => '250', 'name' => '大津'],
            ['area_code' => '260', 'name' => '京都'],
            ['area_code' => '270', 'name' => '大阪'],
            ['area_code' => '280', 'name' => '神戸'],
            ['area_code' => '290', 'name' => '奈良'],
            ['area_code' => '300', 'name' => '和歌山'],
            ['area_code' => '310', 'name' => '鳥取'],
            ['area_code' => '320', 'name' => '松江'],
            ['area_code' => '330', 'name' => '岡山'],
            ['area_code' => '340', 'name' => '広島'],
            ['area_code' => '350', 'name' => '山口'],
            ['area_code' => '360', 'name' => '徳島'],
            ['area_code' => '370', 'name' => '高松'],
            ['area_code' => '380', 'name' => '松山'],
            ['area_code' => '390', 'name' => '高知'],
            ['area_code' => '400', 'name' => '福岡'],
            ['area_code' => '401', 'name' => '北九州'],
            ['area_code' => '410', 'name' => '佐賀'],
            ['area_code' => '420', 'name' => '長崎'],
            ['area_code' => '430', 'name' => '熊本'],
            ['area_code' => '440', 'name' => '大分'],
            ['area_code' => '450', 'name' => '宮崎'],
            ['area_code' => '460', 'name' => '鹿児島'],
            ['area_code' => '470', 'name' => '沖縄'],
        ];
        DB::table('nhk_areas')->insert($areas);
    }
}

