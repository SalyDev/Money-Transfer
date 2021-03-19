<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Agence;
use App\Entity\Client;
use DateTime;
use App\Entity\Compte;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use App\Services\CommonFunctionsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;

class CompteController extends AbstractController
{
    private $commonFunctionsService, $entityManagerInterface, $validatorInterface, $passwordEncoder, $transactionRepository, $compteRepository, $serializer, $clientRepository;
    public function __construct(CommonFunctionsService $commonFunctionsService, EntityManagerInterface $entityManagerInterface, ValidatorInterface $validatorInterface, UserPasswordEncoderInterface $passwordEncoder, TransactionRepository $transactionRepository, CompteRepository $compteRepository, SerializerInterface $serializer, ClientRepository $clientRepository)
    {
        $this->commonFunctionsService = $commonFunctionsService;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->validatorInterface = $validatorInterface;
        $this->passwordEncoder = $passwordEncoder;
        $this->transactionRepository = $transactionRepository;
        $this->compteRepository = $compteRepository;
        $this->serializer = $serializer;
        $this->clientRepository = $clientRepository;
    }

    /**
     *@Security("is_granted('ROLE_ADMIN_SYSTEME')", message="Accès refusé")
     * @Route(path="api/comptes", methods="post", defaults={"_api_collection_operation_name"="createCompte"})
     */
    public function createCompte(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        // en creant le compte on crée l'agence et on iniatialse une transaction de 700 mille fcfa
        $compte = new Compte();
        $compte->setSolde(700000)
            ->setDateCreation(new DateTime())
            ->setIsActif(true);
        // on crée l'agence pour cet compte
        $agence = new Agence();
        $agence->setIsActif(true);
        foreach ($request["agence"] as $key => $value) {
            if ($key != "users") {
                $setter = 'set' . ucfirst($key);
                $agence->$setter($value);
            } else {
                $user = new User();
                foreach ($value[0] as $key => $valeur) {
                    $setter = 'set' . ucfirst($key);
                    if ($key == "password") {
                        $user->$setter($this->passwordEncoder->encodePassword($user, "passer"));
                    } else {
                        $user->$setter($valeur);
                    }
                }
                $this->commonFunctionsService->setRoleOfUser($user, 'admin_agence');
                $user->setIsActif(true);
                $agence->addUser($user);
            }
        }
        $compte->setAgence($agence);
        $this->commonFunctionsService->generateCountCode($compte);
        $this->validatorInterface->validate($agence);
        $this->validatorInterface->validate($user);
        $this->validatorInterface->validate($compte);
        $this->entityManagerInterface->persist($agence);
        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->persist($compte);
        $this->entityManagerInterface->flush();
        return $this->json($compte, 200);
    }

    // fonction permettant d'affichier les parts à verser à l'etat
    // et aux agences suivant une periode
    /**
     *@Security("is_granted('ROLE_ADMIN_SYSTEME')", message="Accès refusé")
     * @Route(path="api/transactions/parts", methods="get")
     */
    public function showParts(Request $request)
    {
        $parts = [];
        $date_debut = $request->query->get('debut');
        $date_fin = $request->query->get("fin");
        $request = json_decode($request->getContent(), true);
        $parts["depots"] = $this->transactionRepository->findAgencePartDepot($date_debut, $date_fin);
        $parts["retraits"] = $this->transactionRepository->findAgencePartRetrait($date_debut, $date_fin);
        $parts["etat"] = $this->transactionRepository->findPartEtat($date_debut, $date_fin);
        return $this->json($parts, 200);
    }

    // fonction permettant de faire un depot le compte d'un agence
    /**
     *@Security("is_granted('ROLE_CAISSIER')", message="Accès refusé")
     * @Route(path="api/transactions/comptes", methods="post")
     */
    public function onDepotCompte(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        $compte = $this->compteRepository->findOneBy(["num_compte" => $request["num_compte"]]);
        if ($compte) {
            if (!$compte->getIsActif()) {
                return new JsonResponse("Désolé, votre compte a été bloqué");
            }
            //    on utilise le user connecté comme etant celui qui a effectué la transaction
            $transaction = new Transaction();
            $transaction = $this->commonFunctionsService->setDepotOnAgenceCount($transaction, $compte, $request["montant"], $this->getUser());
            $this->entityManagerInterface->persist($transaction);
            $this->entityManagerInterface->flush();
            return $this->json($transaction, 201);
        }
        return new JsonResponse("Numéro de compte invalide");
    }

    /**
     *@Security("is_granted('ROLE_USER_AGENCE')", message="Accès refusé")
     * @Route(path="api/transactions", methods="post")
     */
    public function doTransaction(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        $transaction = new Transaction();
        $connected_user = $this->getUser();
        // on recupere le compte de l'agence du user
        $agence = $connected_user->getAgence();
        if ($agence->getIsActif() == true) {
            $compte = $this->compteRepository->findOneBy(["agence" => $agence]);
            if ($request["type"] == "depot" && $compte->getSolde() >= 50000) {
                // si par le cni le client_depot exite deja
                $client_depot = $this->clientRepository->findOneBy(["numero_cni" => $request["client_depot"]["numeroCni"]]);
                if($client_depot){
                    $emetteur = $client_depot;
                }
                else{
                    $emetteur = $this->serializer->denormalize($request["client_depot"], "App\Entity\Client");
                }
                $transaction->setClientDepot($emetteur);
                // on reinitialise le solde du compte
                $compte->setSolde($compte->getSolde() - $request["montant"]);
                $beneficiaire = new Client();
                foreach ($request["client_retrait"] as $key => $value) {
                    $setter = 'set'.ucfirst($key);
                    $beneficiaire->$setter($value);
                }
                $transaction->setClientRetrait($beneficiaire);
                $transaction->setFrais($request["frais"]);
                $transaction->setUserAgenceDepot($connected_user);
                $transaction->setDateDepot(new DateTime());
                $this->commonFunctionsService->setPartsAndCost($transaction, $request["montant"]);
                $this->validatorInterface->validate($emetteur);
                $this->validatorInterface->validate($beneficiaire);
                $this->entityManagerInterface->persist($emetteur);
                $this->entityManagerInterface->persist($beneficiaire);
            }
            if($request["type"] == "retrait"){
                $transaction = $this->transactionRepository->findOneBy(["code" => $request["code"]]);
                // on teste si le retrait n'est pas encore déja fait
                if($transaction->getDateRetrait()){
                    return new JsonResponse("Le retrait a été déjà effectué");
                }
                $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                $transaction->getClientRetrait()->setNumeroCni($request["client_retrait"]["numero_cni"]);
                $transaction->setUserAgenceRetrait($connected_user);
                $transaction->setDateRetrait(new DateTime());
            }
            $this->entityManagerInterface->persist($transaction);
            $this->entityManagerInterface->flush();
            return $this->json($transaction, 200);
            
        }
        return new JsonResponse("Transaction impossible, le copte a été bloqué");
    }

    // fonction permettant d'afficher les commissions d'une agence
    // on recupere l'agence du user connecté
     /**
     *@Security("is_granted('ROLE_USER_AGENCE')", message="Accès refusé")
     * @Route(path="api/agence/commissions", methods="get")
     */
    public function getCommissions(Request $request)
    {
        $date_debut = $request->query->get('debut');
        $date_fin = $request->query->get("fin");
        $connected_user = $this->getUser();
        $agence = $connected_user->getAgence();
        $commissions["retraits"] = $this->transactionRepository->findAgencePartRetrait($date_debut, $date_fin, $agence->getId());
        $commissions["depots"] = $this->transactionRepository->findAgencePartDepot($date_debut, $date_fin, $agence->getId());
        return $this->json($commissions, 200);
    }

    // fonction qui permet d'afficher l'ensemble des transactions d'une agence
     /**
     *@Security("is_granted('ROLE_USER_AGENCE')", message="Accès refusé")
     * @Route(path="api/agence/transactions", methods="get")
     */
    public function getTransactionsOfAgence(Request $request){
        $date_debut = $request->query->get("debut");
        $date_fin = $request->query->get("fin");
        $agence = $this->getUser()->getAgence();
        $transactions["depot"] = $this->transactionRepository->findDepotsOfAgence($agence, $date_debut, $date_fin);
        $transactions["retrait"] = $this->transactionRepository->findRetraitsOfAgence($agence, $date_debut, $date_fin);
        return $this->json($transactions, 200);
    }

    // fonction qui permet de lister les transaction d'un user d'un agence
    // permet aussi de lister mes commissions
     /**
     *@Security("is_granted('ROLE_USER_AGENCE')", message="Accès refusé")
     * @Route(path="api/agence/user/transactions", methods="get")
     */
    public function getTransactionsOfUserAgence(Request $request){
        $date_debut = $request->query->get("debut");
        $date_fin = $request->query->get("fin");
        $connected_user = $this->getUser();
        $transactions = $this->transactionRepository->findUserTransactions($connected_user, $date_debut, $date_fin);
        return $this->json($transactions, 200);
    }

    // fonction permettant de calculer les frais d'une transaction
     /**
     * @Route(path="api/calculator", methods="post")
     */
    public function calculate(Request $request){
        $request = json_decode($request->getContent(), true);
        return $this->json($this->commonFunctionsService->calculateCost($request['montant']), 200);
    }
}
