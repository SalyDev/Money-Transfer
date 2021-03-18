<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $libelles = ["admin_systeme", "caissier", "admin_agence", "user_agence"];
        for ($i=0; $i<count($libelles); $i++) {
            $role = new Role();
            $this->addReference($i, $role);
            $role->setLibelle($libelles[$i]);
            $manager->persist($role);
        }
        $manager->flush();
    }
}
