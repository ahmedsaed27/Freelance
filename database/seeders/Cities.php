<?php

namespace Database\Seeders;

use App\Models\Cities as ModelsCities;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Cities extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = file_get_contents(public_path('egypt.json'));

        $data = json_decode($file, true);


        foreach ($data as $entry) {
            $id = $entry['id'];
            $governorateNameAr = $entry['governorate_name_ar'];
            $governorateNameEn = $entry['governorate_name_en'];


            ModelsCities::create([
                'id' => $id,
                'governorate_name_ar' => $governorateNameAr,
                'governorate_name_en' => $governorateNameEn
            ]);

        }

    }
}
