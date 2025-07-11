<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            RoleSeeder::class,
            BuddiesTableSeeder::class,
            FriendshipsTableSeeder::class,
            ActivitiesTableSeeder::class,
        ]);
    }
}
