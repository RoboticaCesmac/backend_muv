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
            ->statePointsRequired(0)
            ->stateIconPath('images/levels/IconeGrama.png')
            ->stateIsDefault(true)
            ->create();

        UserLevelsFactory::new()
            ->stateLevelNumber(2)
            ->statePointsRequired(100)
            ->stateIconPath('images/levels/IconeArbusto.png')
            ->stateIsDefault(true)
            ->create();
        
        UserLevelsFactory::new()
            ->stateLevelNumber(3)
            ->statePointsRequired(250)
            ->stateIconPath('images/levels/IconeFlor.png')
            ->stateIsDefault(true)
            ->create();
        
        UserLevelsFactory::new()
            ->stateLevelNumber(4)
            ->statePointsRequired(500)
            ->stateIconPath('images/levels/IconeArvore.png')
            ->stateIsDefault(true)
            ->create();
    }
}