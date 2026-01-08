<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imageContents = file_get_contents('https://assets.nsd.co.id/images/kampus/logo/download_(19).png');
        if ($imageContents === false) {
            throw new \Exception('Could not get contents from URL.');
        }
        $imageName = Str::random(20) . '.jpg';
        $imagePath = 'setting/' . $imageName;
        Storage::disk('public')->put($imagePath, $imageContents);

        Log::info('Image for Library System saved to ' . $imagePath);
        Setting::create([
            'name' => "MADRASAH ALIYAH MAMBA'UL ULUM",
            'logo' => $imagePath,
            'address' => '9M89+V87, Jambi,Talang Bakung, Paal Merah, Kec, Kota Jambi, Jambi 36139',
            'phone' => '089786545677',
            'limit_day' => 7,
            'max_borrow' => 3,
        ]);
    }
}
