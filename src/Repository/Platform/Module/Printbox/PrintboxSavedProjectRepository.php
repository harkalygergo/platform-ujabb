<?php

namespace App\Repository\Platform\Module\Printbox;

use App\Entity\Platform\Module\Printbox\SavedProjects;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SavedProjects|null find($id, $lockMode = null, $lockVersion = null)
 * @method SavedProjects|null findOneBy(array $criteria, array $orderBy = null)
 * @method SavedProjects[]    findAll()
 * @method SavedProjects[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintboxSavedProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavedProjects::class);
    }

    // /**
    //  * @return SavedProjects[] Returns an array of SavedProjects objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SavedProjects
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function removeSavedProject(int $id, string $customer, string $projectHash): int
    {
        return $this->createQueryBuilder('p')
            ->delete()
            ->where('p.id = :id')
            ->andWhere('p.customer = :customer')
            ->andWhere('p.projectHash = :projectHash')
            ->setParameter('id', $id)
            ->setParameter('customer', $customer)
            ->setParameter('projectHash', $projectHash)
            ->getQuery()
            ->execute();
    }
}
