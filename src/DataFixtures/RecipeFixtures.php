<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RecipeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        //catégories
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->region())
                ->setDescription($faker->paragraph());
            $manager->persist($category);

            //user
            $password = $faker->password();
            $user = new User();
            $user->setEmail($faker->email())
                ->setUsername($faker->lastname())
                ->setPassword($password);
            $manager->persist($user);

            //recettes
            for ($j = 1; $j <= 4; $j++) {
                $recipe = new Recipe();
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';
                $recipe->setTitle($faker->foodName())
                    ->setAuthor($faker->name())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-9 months'))
                    ->setCategory($category)
                    ->setUser($user);
                $manager->persist($recipe);

                for ($l = 1; $l <= 3; $l++) {
                    //ingrédients
                    $ingredient = new Ingredient();
                    $ingredient->setName($faker->vegetableName());

                    $manager->persist($ingredient);


                }

                for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $days = (new \DateTime())->diff($recipe->getCreatedAt())->days;

                    $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                        ->setRecipe($recipe);

                    $manager->persist($comment);
                }
            }
        }


        $manager->flush();
    }
}
