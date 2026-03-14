<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = json_decode(
            File::get(database_path('data/current-packages.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        foreach ($packages as $package) {
            Package::query()->updateOrCreate(
                ['slug' => $package['slug']],
                $package,
            );
        }
    }
}
