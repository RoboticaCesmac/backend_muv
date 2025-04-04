<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        UserFactory::new()
            ->stateEmail('admin@gmail.com')
            ->statePassword('ADMIN')
            ->stateIsAdmin(true)
            ->stateUserName('Admin')
            ->create();    
    }
}