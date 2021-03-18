<?php

namespace App\Services;

use App\Repository\RoleRepository;
use DateTime;

class CommonFunctionsService{
    private $roleRepository;
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    // foncton permettant de fixer le role(status) et le roles du User
    public function setRoleOfUser($user, $mot_cle)
    {
        $role = $this->roleRepository->findOneBy(["libelle" => $mot_cle]);
        $user->setRoleUser($role);
        $roles[] = 'ROLE_'.strtoupper($mot_cle);
        $user->setRoles($roles);
    }

    // fonction permettant de calculer les frais
    public function calculateCost($montant){
        $frais = 0;
        switch ($montant) {
            case $montant > 0 && $montant <= 5000:
                $frais = 425;
                // $transaction->setFrais(425);
                break;
            case $montant > 5000 && $montant <= 10000:
                // $transaction->setFrais(850);
                $frais = 850;
                break;
            case $montant > 10000 && $montant <= 15000:
                // $transaction->setFrais(1270);
                $frais = 1270;
                break;
            case $montant > 15000 && $montant <= 20000:
                // $transaction->setFrais(1695);
                $frais = 1695;
                break;
            case $montant > 20000 && $montant <= 50000:
                // $transaction->setFrais(2500);
                $frais = 2500;
                break;
            case $montant > 50000 && $montant <= 60000:
                // $transaction->setFrais(3000);
                $frais = 3000;
                break;   
            case $montant > 60000 && $montant <= 75000:
                // $transaction->setFrais(4000);
                $frais = 4000;
                break; 
            case $montant > 75000 && $montant <= 120000:
                // $transaction->setFrais(5000);
                $frais = 5000;
                break;
            case $montant > 120000 && $montant <= 150000:
                // $transaction->setFrais(6000);
                $frais = 6000;
                break;
            case $montant > 150000 && $montant <= 200000:
                // $transaction->setFrais(7000);
                $frais = 7000;
                break;
            case $montant > 200000 && $montant <= 250000:
                // $transaction->setFrais(8000);
                $frais = 8000;
                break;
            case $montant > 250000 && $montant <= 300000:
                // $transaction->setFrais(9000);
                $frais = 9000;
                break;
            case $montant > 300000 && $montant <= 400000:
                // $transaction->setFrais(12000);
                $frais = 12000;
                break;
            case $montant > 400000 && $montant <= 750000:
                // $transaction->setFrais(15000);
                $frais = 15000;
                break;
            case $montant > 750000 && $montant <= 900000:
                // $transaction->setFrais(22000);
                $frais = 22000;
                break;
            case $montant > 900000 && $montant <= 1000000:
                // $transaction->setFrais(25000);
                $frais = 25000;
                break;
            case $montant > 1000000 && $montant <= 1125000:
                // $transaction->setFrais(27000);
                $frais = 270000;
                break;
            case $montant > 1125000 && $montant <= 1400000:
                // $transaction->setFrais(30000);
                $frais = 30000;
                break;
            case $montant > 1400000 && $montant < 2000000:
                // $transaction->setFrais(40000);
                $frais = 40000;
                break;
            case $montant >= 2000000:
                // $transaction->setFrais($montant*0.2);
                $frais = $montant*0.2;
                break;
            default:
                break;
        }
        return $frais;
    }

    // fonction permettant de fixer les parts lors d'une transaction
    public function setPartsAndCost($transaction, $montant){
        // on fixe les frais de transaction
        // switch ($montant) {
        //     case $montant > 0 && $montant <= 5000:
        //         $transaction->setFrais(425);
        //         break;
        //     case $montant > 5000 && $montant <= 10000:
        //         $transaction->setFrais(850);
        //         break;
        //     case $montant > 10000 && $montant <= 15000:
        //         $transaction->setFrais(1270);
        //         break;
        //     case $montant > 15000 && $montant <= 20000:
        //         $transaction->setFrais(1695);
        //         break;
        //     case $montant > 20000 && $montant <= 50000:
        //         $transaction->setFrais(2500);
        //         break;
        //     case $montant > 50000 && $montant <= 60000:
        //         $transaction->setFrais(3000);
        //         break;   
        //     case $montant > 60000 && $montant <= 75000:
        //         $transaction->setFrais(4000);
        //         break; 
        //     case $montant > 75000 && $montant <= 120000:
        //         $transaction->setFrais(5000);
        //         break;
        //     case $montant > 120000 && $montant <= 150000:
        //         $transaction->setFrais(6000);
        //         break;
        //     case $montant > 150000 && $montant <= 200000:
        //         $transaction->setFrais(7000);
        //         break;
        //     case $montant > 200000 && $montant <= 250000:
        //         $transaction->setFrais(8000);
        //         break;
        //     case $montant > 250000 && $montant <= 300000:
        //         $transaction->setFrais(9000);
        //         break;
        //     case $montant > 300000 && $montant <= 400000:
        //         $transaction->setFrais(12000);
        //         break;
        //     case $montant > 400000 && $montant <= 750000:
        //         $transaction->setFrais(15000);
        //         break;
        //     case $montant > 750000 && $montant <= 900000:
        //         $transaction->setFrais(22000);
        //         break;
        //     case $montant > 900000 && $montant <= 1000000:
        //         $transaction->setFrais(25000);
        //         break;
        //     case $montant > 1000000 && $montant <= 1125000:
        //         $transaction->setFrais(27000);
        //         break;
        //     case $montant > 1125000 && $montant <= 1400000:
        //         $transaction->setFrais(30000);
        //         break;
        //     case $montant > 1400000 && $montant < 2000000:
        //         $transaction->setFrais(40000);
        //         break;
        //     case $montant >= 2000000:
        //         $transaction->setFrais($montant*0.2);
        //         break;
        //     default:
        //         break;
        // }
        // commissions
        // $frais = $this->calculateCost($montant);
        // les frais doivent provenir du body
        // $frais = $request[]
        // $transaction->setFrais($frais);
        $transaction->setPartEtat($transaction->getFrais()*0.4);
        $transaction->setPartAgenceDepot($transaction->getFrais()*0.1);
        $transaction->setPartAgenceRetrait($transaction->getFrais()*0.2);
        $transaction->setPartSysteme($transaction->getFrais()*0.3);
        $transaction->setMontant($montant);
        // $transaction->setType("Dépôt");
        $transaction->setCode(random_int(100 , 999).''.($transaction->getDateDepot())->format('His'));
    }

    // fonction permettant de générer le code d'un compte
    public function generateCountCode($compte){
        $agence = $compte->getAgence();
        $date_creation = $compte->getDateCreation();
        $code = str_replace(' ', '', $agence->getNom()).''.$date_creation->format('His');
        $compte->setNumCompte($code);
    }

    // fonction permettant de réinitialiser le solde d'un compte
    // agence lorsqu'il y'a depot par l'agence
    public function setDepotOnAgenceCount($transaction, $compte, $montant, $user){
        $compte->setSolde($compte->getSolde() + $montant);
        $transaction->setMontant($montant);
        // $transaction->setCompte($compte);
        $transaction->setFrais(0);
        $transaction->setPartEtat(0);
        $transaction->setPartAgenceDepot(0);
        $transaction->setPartAgenceRetrait(0);
        $transaction->setPartSysteme(0);
        $transaction->setDateDepot(new DateTime());
        $transaction->setUserAgenceDepot($user);
        $code = random_int(100 , 999).''.($transaction->getDateDepot())->format('His');
        $transaction->setCode($code);
        return $transaction;
    }
}