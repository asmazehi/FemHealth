<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function add(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evenement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    

    public function findByNom(string $searchTerm): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nom LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%')
            ->getQuery()
            ->getResult();
    }
    


public function countSignaledEvents(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->where('e.signaler = :signaled')
            ->setParameter('signaled', true)
            ->getQuery()
            ->getSingleScalarResult();
    }


 /**
     * Récupère les événements avec un montant inférieur à 500.
     *
     * @return Evenement[] Returns an array of Evenement objects
     */
    public function findByMontantInf500(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.montant < :montant')
            ->setParameter('montant', 500)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les événements avec un montant supérieur à 500.
     *
     * @return Evenement[] Returns an array of Evenement objects
     */
    public function findByMontantSup500(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.montant > :montant')
            ->setParameter('montant', 500)
            ->getQuery()
            ->getResult();
    }
}

  



   
//    /**
//     * @return Evenement[] Returns an array of Evenement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

