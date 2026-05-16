<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'emterule2004@sys.test.ph'],
            [
                'full_name'     => 'Evangeline M. Teruel',
                'password_hash' => Hash::make('OLFU-ANT-NURSING'),
                'is_active'     => 1,
            ]
        );
    }
}
