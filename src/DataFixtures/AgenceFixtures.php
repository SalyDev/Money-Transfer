<?php

namespace App\DataFixtures;

use App\Entity\Agence;
use App\Entity\Compte;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AgenceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $numeros = ["770923627", "774876544", "768670945", "705674321"];
        foreach ($numeros as $key => $numero) {
            # code...
            // $compte = new Compte();
            $agence = new Agence();
            $this->addReference('agence'.$key, $agence);
            $agence->setNom($faker->company)
                    ->setTelephone($numero)
                    ->setAdresse($faker->address)
                    ->setLongitude($faker->longitude)
                    ->setLatitude($faker->latitude);
            $compte = new Compte();
            $compte->setNumCompte($faker->bankAccountNumber)
                    ->setDateCreation(new DateTime())
                    ->setAgence($agence);
            $manager->persist($agence);
            $manager->persist($compte);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
