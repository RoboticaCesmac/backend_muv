<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        UserFactory::new()
            ->stateEmail('ADMIN')
            ->statePassword('ADMIN')
            ->stateIsAdmin(true)
            ->stateUserName('Admin')
            ->create();    
    }
}