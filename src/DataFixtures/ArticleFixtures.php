<?php

namespace App\DataFixtures;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Création de 3 catégorie
        $faker = \Faker\Factory::create('fr_FR');

        $categories = [
            'homeAppliance' => 'Electroménager',
            'cosmetic' => 'Cosmétique',
            'electronic' => 'Electronique',
            'clothing' => 'Habillement',
            'food' => 'Aliments et Boissons'
        ];

        foreach($categories as $value) {
            $category = new Category();
            $category->setName($value)
                     ->setDescription($faker->paragraph(random_int(1, 3)));

            $manager->persist($category);
        }

        $manager->flush();
    }
}
