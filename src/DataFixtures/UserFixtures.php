<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $userPasswordEncoderInterface;
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i=0; $i<4 ; $i++) { 
            $status = $this->getReference($i);
            $roles[$i][] = 'ROLE_'.strtoupper($status->getLibelle());
            for ($j=0; $j<5 ; $j++) { 
                $user = new User;
                $user->setPrenom($faker->firstName())
                    ->setNom($faker->lastName())
                    ->setEmail($faker->email)
                    ->setTelephone($faker->unique()->phoneNumber)
                    ->setEmail($faker->email)
                    ->setPassword($this->userPasswordEncoderInterface->encodePassword($user, 'passer'))
                    ->setRoleUser($this->getReference($i))
                    ->setIsActif(true);
                $user->setRoles($roles[$i]);
                if($status->getLibelle()=="admin_agence" || $status->getLibelle()=="user_agence"){
                    $user->setAgence($this->getReference('agence'.$i));
                    // $this->addReference('user')
                    // $user->addDepotsAgence();
                    if($status->getLibelle()=="user_agence"){
                        // $user->addDepotsAgence();
                        $this->addReference('user'.$j, $user);
                    }
                }
                $manager->persist($user);
            }
        }
        $manager->flush();
    }
    public function getDependencies()
   {
       return array(
           RoleFixtures::class,
           AgenceFixtures::class,
       );
   }

}
