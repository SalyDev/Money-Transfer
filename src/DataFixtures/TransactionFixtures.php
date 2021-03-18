<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Transaction;
use App\Services\CommonFunctionsService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    private $commonFunctionsService;
    public function __construct(CommonFunctionsService $commonFunctionsService)
    {
        $this->commonFunctionsService = $commonFunctionsService;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i=0; $i < 4; $i++) { 
        $transaction = new Transaction();
        $transaction->setMontant($faker->numberBetween(5000, 2000000));
        $transaction->setUserAgenceDepot($this->getReference('user'.$i));
        $transaction->setUserAgenceRetrait($this->getReference('user'.($i+1)));
        $client_depot = new Client();
        $client_depot->setNom($faker->lastName());
        $client_depot->setPrenom($faker->firstName());
        $client_depot->setTelephone($faker->unique()->phoneNumber);
        $client_depot->setNumeroCni($faker->unique()->numberBetween(1000000, 9999999));
        $transaction->setClientDepot($client_depot);
        $transaction->setDateDepot($faker->dateTimeBetween('2020-08-05 13:10:46','2020-11-05 13:10:46'));
        $client_retrait = new Client();
        $client_retrait->setNom($faker->lastName());
        $client_retrait->setPrenom($faker->firstName());
        $client_retrait->setTelephone($faker->unique()->phoneNumber);
        $client_retrait->setNumeroCni($faker->unique()->numberBetween(1000000, 9999999));
        $transaction->setClientRetrait($client_retrait);
        $transaction->setDateRetrait($faker->dateTime());
        $transaction->setDateRetrait($faker->dateTimeBetween('2020-12-05 13:10:46','2021-02-05 13:10:46'));
        $this->commonFunctionsService->setPartsAndCost($transaction, $transaction->getMontant());
        $manager->persist($client_depot);
        $manager->persist($client_retrait);
        $manager->persist($transaction);;
    }
    $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
