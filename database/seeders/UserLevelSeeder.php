<?php

namespace Database\Seeders;

use Database\Factories\UserLevelsFactory;
use Illuminate\Database\Seeder;

class UserLevelSeeder extends Seeder
{
    public function run()
    {
        UserLevelsFactory::new()
            ->stateLevelNumber(1)
            ->stateCarbonFootprintRequired(0)
            ->stateIconPath('images/levels/IconeGrama.png')
            ->stateIsDefault(true)
            ->create();

        UserLevelsFactory::new()
            ->stateLevelNumber(2)
            ->stateCarbonFootprintRequired(100)
            ->stateIconPath('images/levels/IconeArbusto.png')
            ->stateIsDefault(true)
            ->create();
        
        UserLevelsFactory::new()
            ->stateLevelNumber(3)
            ->stateCarbonFootprintRequired(250)
            ->stateIconPath('images/levels/IconeFlor.png')
            ->stateIsDefault(true)
            ->create();
        
        UserLevelsFactory::new()
            ->stateLevelNumber(4)
            ->stateCarbonFootprintRequired(500)
            ->stateIconPath('images/levels/IconeArvore.png')
            ->stateIsDefault(true)
            ->create();
    }
}