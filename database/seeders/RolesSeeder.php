<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reader = Roles::firstOrCreate(['name' => 'reader']);

        Roles::firstOrCreate(['name' => 'moderator']);

        User::whereNull('role_id')->update([
            'role_id' => $reader->id,
        ]);
    }
}
