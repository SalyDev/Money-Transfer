<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Compte;
use App\Services\CommonFunctionsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class CompteDataPersister implements ContextAwareDataPersisterInterface
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
      $data->setDateCreation(new DateTime());
      $this->manager->persist($data);
      $this->manager->flush();
      return $data;
    }

    public function remove($data, array $context = [])
    {
      // call your persistence layer to delete $data
      $data->setIsActif(false);
    //   on bloque aussi l'agence qui a ce compte
      $agence = $data->getAgence();
      $agence->setIsActif(false);
    //   et les users de cette agence
        $users = $agence->getUsers();
        foreach ($users as $user) {
            $user->setIsActif(false);
        }
     
      $this->manager->flush();
    }
}