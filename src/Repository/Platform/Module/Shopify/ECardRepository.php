<?php

namespace App\Repository\Platform\Module\Shopify;

use App\Entity\Platform\Module\Shopify\ECard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ECardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ECard::class);
    }
}
