<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\CommonFunctionsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Repository\ClientRepository;

class UserController extends AbstractController
{
    private $userRepository, $roleRepository, $passwordEncoder, $manager, $validatorInterface, $commonFunctionsService;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $manager, ValidatorInterface $validatorInterface, CommonFunctionsService $commonFunctionsService)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->validatorInterface = $validatorInterface;
        $this->commonFunctionsService = $commonFunctionsService;
    }
    // fonction permettant de lister les caissiers

    /**
     * @Security("is_granted('ROLE_ADMIN_SYSTEME')", message="Accès refusé")
     * @Route(path="api/caissiers", methods="get")
     */
    public function showCaissiers()
    {
        $role = $this->roleRepository->findOneBy(["libelle" => "caissier"]);
        $caissiers = $this->userRepository->findBy([
            "role_user" => $role,
            "is_actif" => true
        ]);
        return $this->json($caissiers, 200);
    }

    // fonction permettant de lister les users de l'agence
    /**
     * @Security("is_granted('ROLE_ADMIN_AGENCE')", message="Accès refusé")
     * @Route(path="api/users_agence", methods="get")
     */
    public function listUserAgence()
    {
        $role = $this->roleRepository->findOneBy(["libelle" => "user_agence"]);
        // on recupere l'agence du user connecté
        $agence = $this->getUser()->getAgence();
        $users_agence = $this->userRepository->findBy([
            "role_user" => $role,
            "agence" => $agence,
            "is_actif" => true
        ]);
        return $this->json($users_agence, 200);
    }

    //   fonction permettant de créer un user agence
    /**
     * @Security("is_granted('ROLE_ADMIN_AGENCE')", message="Accès refusé")
     * @Route(path="api/users_agence", methods="post")
     */
    public function createUser(Request $request)
    {
        $user = new User();
        $avatar = $request->files->get('avatar');
        $avatarbin = fopen($avatar, 'rb');
        $user->setAvatar($avatarbin);
        $request = $request->request->all();
        foreach ($request as $key => $value) {
            if ($key == "password") {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $value));
            } else {
                $setter = 'set' . ucfirst($key);
                $user->$setter($value);
            }
        }
        //    pour l'agence on recupere l'agence de l'utilisateur connecté
        $user->setAgence($this->getUser()->getAgence());
        $this->commonFunctionsService->setRoleOfUser($user, "user_agence");
        $this->validatorInterface->validate($user);
        $this->manager->persist($user);
        $this->manager->flush();
        fclose($avatarbin);
        return $this->json($user, 200);
    }

      /**
     * @Security("is_granted('ROLE_USER_AGENCE')", message="Accès refusé")
     * @Route(path="api/getclients", methods="get")
     */
    // fonction qui permet de trouver un client par son numero cni
    public function getClientByCni(Request $request, ClientRepository $clientRepository){
        $numCni = $request->query->get('cni');
        $client_depot = $clientRepository->findOneBy(["numero_cni" => $numCni]);
        return $this->json($client_depot);
    }
}
