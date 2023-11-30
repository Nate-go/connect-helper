<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Constants\UserConstant\UserRole;
use App\Constants\UserConstant\UserStatus;
use App\Models\Enterprise;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $enterprise = Enterprise::create([
            'name' => 'Admin',
        ]);

        User::create([
            'name' => 'THAdmin',
            'enterprise_id' => $enterprise->id,
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
            'image_url' => 'https://res.cloudinary.com/dsrtzowwc/image/upload/v1700325399/samples/animals/reindeer.jpg',
        ]);
    }
}
