<?php

namespace EducHack\Repository;

use Doctrine\ORM\EntityRepository;

class CDNHitRepository extends EntityRepository
{
    public function fetchLast()
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
