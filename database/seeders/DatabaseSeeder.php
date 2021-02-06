<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Subscription;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $utc_6 = new DateTime('now', new DateTimeZone('-0600'));
        $utc_6->add(new \DateInterval('PT' . 10 . 'M'));
        $new_expire = $utc_6->format('Y-m-d H:i:s');

        for ($i=0; $i < 1000000; $i++) {
            $create_device = Device::create([
                'device_uuid' => Str::uuid(),
                'app_id' => Str::random(10),
                'language' => "en",
                'operation_system' => "Android",
            ]);
            Subscription::create([
                'device_id' => $create_device->id,
                'status' => true,
                'expire_at' => $new_expire
            ]);
        }
    }
}
