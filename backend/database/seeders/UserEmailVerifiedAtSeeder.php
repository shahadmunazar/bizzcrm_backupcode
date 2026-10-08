<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserEmailVerifiedAtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all users so their email_verified_at matches their created_at time
        \Illuminate\Support\Facades\DB::table('users')->update([
            'email_verified_at' => \Illuminate\Support\Facades\DB::raw('created_at')
        ]);
    }
}
