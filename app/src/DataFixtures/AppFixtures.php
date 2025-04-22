<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $description = $faker->paragraph;

            $book = new Book();
            $book
                ->setTitle($faker->sentence(3))
                ->setAuthor($faker->name)
                ->setPublishedAt($faker->dateTimeBetween('-30 years', 'now'))
                ->setDescription($description)
                ->setIsbn($faker->isbn13)
            ;
            $manager->persist($book);
        }

        $manager->flush();
    }
}
