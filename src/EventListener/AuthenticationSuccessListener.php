<?php
namespace App\EventListener;

use App\Repository\CompteRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener{

    private $compteRepository;
    public function __construct(CompteRepository $compteRepository)
    {
       $this->compteRepository = $compteRepository; 
    }

/**
 * @param AuthenticationSuccessEvent $event
 */
public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
{
    $data = $event->getData();
    $user = $event->getUser();

    $is_actif = $user->getIsActif();

    if (!$user instanceof UserInterface) {
        return;
    }
    else{
        if(!$is_actif){
            $expiredTokenException = new InvalidTokenException();
            throw $expiredTokenException;
        }
        else{
            $compte = $this->compteRepository->findOneBy(["agence" => $user->getAgence()]);
            $infos = array(
                'token' => $data["token"],
                'roles' => $user->getRoles(),
                'avatar' => $user->getAvatar(),
                'prenom' => $user->getPrenom(),
                'nom' => $user->getNom(),
                'solde' => $compte->getSolde(),
            );
            $event->setData($infos);
        }
    }
}
}
