<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Administrator
        $friolo = new User();
        $friolo
            ->setEmail('friolo@yopmail.com')
            ->setFirstName('Fabrice')
            ->setLastName('Riolo')
            ->setPassword(
                $this->passwordHasher->hashPassword($friolo, 'friolo')
            )
            ->setRoles(['ROLE_ADMIN', 'ROLE_AGENT']);

        $manager->persist($friolo);

        // First real estate agent
        $mmartin = new User();
        $mmartin
            ->setEmail('mmartin@yopmail.com')
            ->setFirstName('Marie')
            ->setLastName('Martin')
            ->setPassword(
                $this->passwordHasher->hashPassword($mmartin, 'mmartin')
            )
            ->setRoles(['ROLE_AGENT']);

        $manager->persist($mmartin);
        $this->addReference('agent_mmartin', $mmartin);

        // Second real estate agent
        $pbernard = new User();
        $pbernard
            ->setEmail('pbernard@yopmail.com')
            ->setFirstName('Pierre')
            ->setLastName('Bernard')
            ->setPassword(
                $this->passwordHasher->hashPassword($pbernard, 'pbernard')
            )
            ->setRoles(['ROLE_AGENT']);

        $manager->persist($pbernard);
        $this->addReference('agent_pbernard', $pbernard);

        // Third real estate agent
        $laugeraud = new User();
        $laugeraud
            ->setEmail('laugeraud@yopmail.com')
            ->setFirstName('Luc')
            ->setLastName('Augeraud')
            ->setPassword(
                $this->passwordHasher->hashPassword($laugeraud, 'laugeraud')
            )
            ->setRoles(['ROLE_AGENT']);

        $manager->persist($laugeraud);
        $this->addReference('agent_laugeraud', $laugeraud);

        $manager->flush();
    }
}