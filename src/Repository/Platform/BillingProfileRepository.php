<?php

namespace App\Repository\Platform;

use App\Entity\Platform\BillingProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BillingProfile>
 *
 * @method BillingProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method BillingProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method BillingProfile[]    findAll()
 * @method BillingProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillingProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillingProfile::class);
    }

    //    /**
    //     * @return BillingAccount[] Returns an array of BillingAccount objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BillingAccount
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
