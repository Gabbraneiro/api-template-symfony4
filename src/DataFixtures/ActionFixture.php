<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Action;

class ActionFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $action = new Action(
            'VIEW_PACIENTES',
            'Ver pacientes'
        );
        $manager->persist($action);
        $manager->flush();
    }
}
