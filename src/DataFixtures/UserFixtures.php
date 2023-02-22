<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Ingredients;
use App\Entity\Meal;
use App\Entity\Member;
use App\Entity\PrivateMessage;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Query\AST\Functions\CurrentTimeFunction;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new Member();
            $user->setEmail("mohamed@gmail.com")
                ->setPassword("3ezdin")
                ->setDescription("Hedha member mel brima")
                ->setPhoneNumber("Normalement mayjich222345")
                ->setFirstName("mohamed")
                ->setLastName("benFarah");

        $user1 = new Admin();
        $user1->setEmail("Ahmed@gmail.com")
            ->setPassword("Omwee")
            ->setFirstName("Ahmed")
            ->setLastName("Ouni");

        $privateMessage = new PrivateMessage();
        $privateMessage->setIdFirstUser($user)
                        ->setIdSecondUser($user1)
                        ->setContent("Wee Ahmado");


        $privateMessage1 = new PrivateMessage();
        $privateMessage1->setIdFirstUser($user1)
            ->setIdSecondUser($user)
            ->setContent("Wee dali");


        $ingredient = new Ingredients();
        $ingredient->setName("eggs")
                   ->setCalories(12)
                    ->setProtein(10)
                    ->setTotalFat(6);

        $ingredient2 = new Ingredients();
        $ingredient2->setName("escalope")
            ->setCalories(16)
            ->setProtein(9)
            ->setTotalFat(7);

        $ingredient3 = new Ingredients();
        $ingredient3->setName("ma9rouna")
            ->setCalories(9)
            ->setProtein(6)
            ->setTotalFat(9);

        $ingredient4 = new Ingredients();
        $ingredient4->setName("tmatem")
            ->setCalories(12)
            ->setProtein(3)
            ->setTotalFat(2);

        $recipe = new Recipe();
        $recipe->setName("Ma9rouna 7amra")
                ->setDescription("test7a9 tmateeem aaa w kifeh nzidha")
                ->addIngredient($ingredient4)
                ->addIngredient($ingredient3);

        $recipe1 = new Recipe();
        $recipe1->setName("escalope w 3dham")
            ->setDescription("tayeb l'escalope wel 7dham jawek behyy")
            ->addIngredient($ingredient)
            ->addIngredient($ingredient2);

        $recipe2 = new Recipe();
        $recipe2->setName("escalope bel tmatem")
            ->setDescription("test7a9 tmateeem w escalope")
            ->addIngredient($ingredient4)
            ->addIngredient($ingredient2);

        $meal = new Meal();
        $meal->setName("ma9rouna w escalope")
            ->setDescription("zouz recipies hedhom")
            ->addRecipe($recipe)
            ->addRecipe($recipe2);

        $meal2 = new Meal();
        $meal2->setName("ma9rouna w escalope w 3dham")
            ->setDescription("3 hedhom")
            ->addRecipe($recipe)
            ->addRecipe($recipe2)
            ->addRecipe($recipe1);

        $manager->persist($user);
        $manager->persist($user1);
        $manager->persist($privateMessage);
        $manager->persist($privateMessage1);
        $manager->persist($ingredient);
        $manager->persist($ingredient2);
        $manager->persist($ingredient3);
        $manager->persist($ingredient4);
        $manager->persist($recipe);
        $manager->persist($recipe1);
        $manager->persist($recipe2);
        $manager->persist($meal);
        $manager->persist($meal2);




        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
