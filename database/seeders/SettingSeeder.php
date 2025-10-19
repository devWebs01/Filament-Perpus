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
        $imageContents = file_get_contents('https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png');
        if ($imageContents === false) {
            throw new \Exception('Could not get contents from URL.');
        }
        $imageName = Str::random(20).'.jpg';
        $imagePath = 'setting/'.$imageName;
        Storage::put($imagePath, $imageContents);

        Log::info('Image for Library System saved to '.$imagePath);
        Setting::create([
            'name' => 'Library System',
            'logo' => $imagePath,
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
        ]);
    }
}
