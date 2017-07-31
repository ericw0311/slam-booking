<?php

namespace SD\CoreBundle\Repository;

/**
 * PlanificationHeaderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlanificationHeaderRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPlanificationHeadersCount($file)
    {
    $queryBuilder = $this->createQueryBuilder('p');
    $queryBuilder->select($queryBuilder->expr()->count('p'));
    $queryBuilder->where('p.file = :file')->setParameter('file', $file);

    $query = $queryBuilder->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }
	
    public function getDisplayedPlanificationHeaders($file, $firstRecordIndex, $maxRecord)
    {
    $queryBuilder = $this->createQueryBuilder('p');
    $queryBuilder->where('p.file = :file')->setParameter('file', $file);
    $queryBuilder->orderBy('p.name', 'ASC');
    $queryBuilder->setFirstResult($firstRecordIndex);
    $queryBuilder->setMaxResults($maxRecord);
   
    $query = $queryBuilder->getQuery();
    $results = $query->getResult();
    return $results;
    }
}
