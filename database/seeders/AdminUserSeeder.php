<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_USER_EMAIL', 'admin@acutetourism.org')],
            [
                'name' => 'Acute Admin',
                'password' => env('ADMIN_USER_PASSWORD', 'Admin@12345'),
                'is_admin' => true,
            ],
        );
    }
}
