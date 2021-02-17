<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Role;

class RoleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $role = new Role(
        //     'SUPER_USER',
        //     'Super usuario'
        // );
        // $manager->persist($role);
        // $manager->flush();
    }
}
