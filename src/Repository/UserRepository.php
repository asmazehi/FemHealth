<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByDte($date): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.registeredAt like :date')
            ->setParameter('date', "%$date%")
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function usersForStat($date): array
    {
        $dateFormat = $date->format('Y-m');
        $result = $this->createQueryBuilder('u')
            ->select('count(u.id) as total , u.registeredAt as registerDate')
            ->Where('u.registeredAt like :date')
//            ->andWhere('u.active = 1')
            ->setParameter('date', "%$dateFormat%")
            ->groupBy('u.registeredAt')
            ->getQuery()
            ->getResult();

        $firstDayOfMonth = clone $date;
        $firstDayOfMonth->modify('first day of this month');
        $lastDayOfMonth = clone $date;
        $lastDayOfMonth->modify('last day of this month');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($firstDayOfMonth, $interval, $lastDayOfMonth);
        // Créer un tableau associatif pour stocker les résultats
        $resultMap = [];
        foreach ($dateRange as $date) {
            $resultMap[$date->format('Y-m-d')] = 0;
        }
        // Mettre à jour les résultats avec les données de la requête
        foreach ($result as $item) {
            if (isset($resultMap[$item['registerDate']->format('Y-m-d')])) {
                $resultMap[$item['registerDate']->format('Y-m-d')] += $item['total'];
            } else {
                $resultMap[$item['registerDate']->format('Y-m-d')] = $item['total'];
            }
        }

        $data = [];
        foreach ($resultMap as $date => $total) {
            $data[] = [
                'registerDate' => $date,
                'value' => $total,
            ];
        }
        return $data;

    }
//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
