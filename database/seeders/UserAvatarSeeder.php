<?php

namespace Database\Seeders;

use Database\Factories\UserAvatarFactory;
use Illuminate\Database\Seeder;

class UserAvatarSeeder extends Seeder
{
    public function run()
    {
        UserAvatarFactory::new()->stateName('Passaro')->stateAvatarPath('images/Avatars/Bird.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Cervo')->stateAvatarPath('images/Avatars/Deer.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Raposa')->stateAvatarPath('images/Avatars/Fox.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Coruja')->stateAvatarPath('images/Avatars/Owl.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Planta')->stateAvatarPath('images/Avatars/Plant.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Porco-Espinho')->stateAvatarPath('images/Avatars/Porcupine.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Árvore')->stateAvatarPath('images/Avatars/Tree.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Tartaruga')->stateAvatarPath('images/Avatars/Turtle.png')->stateIsDefault(true)->create();

        UserAvatarFactory::new()->stateName('Planeta')->stateAvatarPath('images/Avatars/World.png')->stateIsDefault(true)->create();
    }
}