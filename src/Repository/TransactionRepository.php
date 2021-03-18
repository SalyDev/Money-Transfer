<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // fonction permettantde trouver des transactions entre deux dates
    // public function getTransactionBetweenTwoDates($debut, $fin)
    // {
    //     return $this->createQueryBuilder('t')
    //         ->where('t.date_depot BETWEEN :debut AND :fin')
    //         ->setParameter('debut', $debut)
    //         ->setParameter('fin', $fin)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // les parts de dépots d'une agence suivant une période
    public function findAgencePartDepot($debut=null, $fin=null, $id=null)
    {
        $result = $this->createQueryBuilder('t')
            ->join('t.user_agence_depot', 'user')
            ->join('user.agence', 'agence')
            ->select('SUM(t.part_agence_depot) as parts_depot, agence.nom as nom_agence')
            ->groupBy('agence.id');
            if($debut && $fin){
            $result->where('t.date_retrait BETWEEN :debut AND :fin')
                    ->setParameter('debut', $debut)
                    ->setParameter('fin', $fin);
            }
            if($id){
                $result->andWhere('agence.id = :id')
                        ->setParameter('id', $id);
            }
            return $result->getQuery()
                        ->getResult();
    }

    // les parts de retraits d'une agence suivant une periode
    public function findAgencePartRetrait($debut=null, $fin=null, $id=null)
    {
        $result =  $this->createQueryBuilder('t')
            ->join('t.user_agence_retrait', 'user')
            ->join('user.agence', 'agence')
            ->select('SUM(t.part_agence_retrait) as parts_retrait, agence.nom as nom_agence')
            ->groupBy('agence.id');
        if($debut && $fin){
            $result->where('t.date_retrait BETWEEN :debut AND :fin')
                    ->setParameter('debut', $debut)
                    ->setParameter('fin', $fin);
        }
        if($id){
            $result->andWhere('agence.id = :id')
                    ->setParameter('id', $id);
        }
        return $result->getQuery()
                    ->getResult();
    }

     // les parts de dépots d'une agence suivant une période
    //  public function findAgencePart($debut, $fin)
    //  {
    //      return $this->createQueryBuilder('t')
    //          ->join('t.user_agence_depot', 'user')
    //          ->join('user.agence', 'agence')
    //          ->where('t.date_retrait BETWEEN :debut AND :fin')
    //          ->setParameter('debut', $debut)
    //          ->setParameter('fin', $fin)
    //          ->select('SUM(t.part_agence_depot) as parts_depot, SUM(t.part_agence_retrait) as parts_retrait, agence.nom as nom_agence')
    //          ->groupBy('agence.id')
    //          ->getQuery()
    //          ->getResult();
    //  }

    // afficher la part de l'etat suivant une periode
    public function findPartEtat($debut=null, $fin=null)
    {
        $result = $this->createQueryBuilder('t')
                        ->select('SUM(t.part_etat) as parts_etat');
            if($debut && $fin){
                $result->where('t.date_depot BETWEEN :debut AND :fin or t.date_retrait BETWEEN :debut AND :fin')
                        ->setParameter('debut', $debut)
                        ->setParameter('fin', $fin);
            }
            return $result->getQuery()
                            ->getResult();
    }

    // recuperer l'ensemble des transactions de depot d'une agence
    public function findDepotsOfAgence($agence, $debut=null, $fin=null){
        $result = $this->createQueryBuilder('t')
                        ->join('t.user_agence_depot', 'user_agence_depot')
                        ->select('t.date_depot as date, user_agence_depot.prenom as prenom_user,user_agence_depot.nom as nom_user, t.montant as montant, t.frais as frais')
                        ->andWhere('user_agence_depot.agence = :agence')
                        ->setParameter('agence', $agence);
        if($debut && $fin){
            $result->andWhere('t.date_depot BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin);
        }
        return $result->getQuery()
                        ->getResult();
    }

     // recuperer l'ensemble des transactions de retrait d'une agence
     public function findRetraitsOfAgence($agence, $debut=null, $fin=null){
        $result = $this->createQueryBuilder('t')
                        ->join('t.user_agence_retrait', 'user_agence_retrait')
                        ->select('t.id as id, t.date_retrait as date, user_agence_retrait.prenom as prenom_user, user_agence_retrait.nom as nom_user, t.montant as montant, t.frais as frais')
                        ->where('user_agence_retrait.agence = :agence')
                        ->setParameter('agence', $agence);
        if($debut && $fin){
            $result->andWhere('t.date_retrait BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin);
        }
        return $result->getQuery()
                        ->getResult();
    }

    // fonction permettant d'afficher mes transactions
    public function findUserTransactions($user, $debut=null, $fin=null){
        $result = $this->createQueryBuilder('t')
                        // ->select('t.date_depot as date, user_agence_depot.prenom as prenom_user,user_agence_depot.nom as nom_user, t.montant as montant, t.frais as frais')
                        ->andWhere('t.user_agence_depot = :user OR t.user_agence_retrait = :user')
                        ->setParameter('user', $user);
        if($debut && $fin){
            $result->andWhere('t.date_depot BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin);
        }
        return $result->getQuery()
                        ->getResult();
    }

}
