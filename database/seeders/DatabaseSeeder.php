<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
                                 'name'     => 'admin',
                                 'email' => 'admin@aspire.com',
                                 'role'  => 'admin',
                                 'password' => Hash::make('admin@#$%')
                             ]);
        $customer = User::create([
                                  'name'     => 'customer',
                                  'email' => 'customer@aspire.com',
                                  'role'  => 'customer',
                                  'password' => Hash::make('customer@#$%')
                              ]);
    }
}
