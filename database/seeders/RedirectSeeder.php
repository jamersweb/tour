<?php

namespace Database\Seeders;

use App\Models\Redirect;
use Illuminate\Database\Seeder;

class RedirectSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            [
                'source_path' => '/tours',
                'destination_url' => '/experiences',
                'status_code' => 301,
                'is_active' => true,
            ],
            [
                'source_path' => '/tour/private-heritage-desert-safari',
                'destination_url' => '/experiences/private-heritage-desert-safari',
                'status_code' => 301,
                'is_active' => true,
            ],
            [
                'source_path' => '/faqs',
                'destination_url' => '/faq',
                'status_code' => 301,
                'is_active' => true,
            ],
            [
                'source_path' => '/blog',
                'destination_url' => '/journal',
                'status_code' => 301,
                'is_active' => true,
            ],
            [
                'source_path' => '/package/ufc-fight-night-returns-to-abu-dhabi',
                'destination_url' => '/packages/ufc-fight-night-returns-to-abu-dhabi',
                'status_code' => 301,
                'is_active' => true,
            ],
        ])->each(function (array $redirect): void {
            $redirect['source_path'] = Redirect::normalizePath($redirect['source_path']);

            Redirect::updateOrCreate(
                ['source_path' => $redirect['source_path']],
                $redirect,
            );
        });
    }
}
