<?php

namespace EducHack\Repository;

use Doctrine\ORM\EntityRepository;

class PositionRepository extends EntityRepository
{
    public function count()
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
