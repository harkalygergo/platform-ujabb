<?php

namespace App\Repository\Platform;

use App\Entity\Platform\WebsitePage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WebsitePage>
 *
 * @method WebsitePage|null find($id, $lockMode = null, $lockVersion = null)
 * @method WebsitePage|null findOneBy(array $criteria, array $orderBy = null)
 * @method WebsitePage[]    findAll()
 * @method WebsitePage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsitePageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsitePage::class);
    }

    public function findByWebsiteId(int $websiteId): array
    {
        return $this->createQueryBuilder('wp')
            ->andWhere('wp.website = :websiteId')
            ->setParameter('websiteId', $websiteId)
            ->getQuery()
            ->getResult();
    }
}
