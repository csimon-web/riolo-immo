<?php

namespace App\DataFixtures;

use App\Entity\Property;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PropertyFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $agents = [
            $this->getReference('agent_mmartin', User::class),
            $this->getReference('agent_pbernard', User::class),
            $this->getReference('agent_laugeraud', User::class),
        ];

        for ($i = 0; $i < 100; $i++) {
            $property = new Property();

            $property
                ->setTitle($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setSurface($faker->numberBetween(20, 350))
                ->setRooms($faker->numberBetween(2, 10))
                ->setBedrooms($faker->numberBetween(1, 9))
                ->setFloor($faker->numberBetween(0, 15))
                ->setPrice($faker->numberBetween(100000, 1000000))
                ->setHeating(
                    $faker->numberBetween(0, count(Property::HEATING) - 1)
                )
                ->setCity($faker->city)
                ->setAddress($faker->address)
                ->setPostalCode($faker->postcode)
                ->setSold(false)
                ->setAssignedAgent($faker->randomElement($agents));

            $manager->persist($property);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}