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

        UserFactory::new()
            ->stateEmail('gustavo.barrel44@gmail.com')
            ->statePassword('21213300')
            ->stateUserName('Gustavo')
            ->stateVehicleId(2)
            ->stateAvatar(2)
            ->stateIsFirstLogin(false)
            ->stateRouteFinalizada()
            ->create();
    }
}