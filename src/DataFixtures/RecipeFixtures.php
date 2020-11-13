<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RecipeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        

        $manager->flush();
    }
}
