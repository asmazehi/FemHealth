<?php

namespace App\Repository;

use App\Entity\lignepanier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method lignepanier|null find($id, $lockMode = null, $lockVersion = null)
 * @method lignepanier|null findOneBy(array $criteria, array $orderBy = null)
 * @method lignepanier[]    findAll()
 * @method lignepanier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, lignepanier::class);
    }

    // Ajoutez vos méthodes de requête personnalisées ici
}
