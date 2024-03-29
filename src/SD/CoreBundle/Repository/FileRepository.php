<?php

namespace SD\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

/**
 * FileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FileRepository extends \Doctrine\ORM\EntityRepository
{
    // Retourne les dossiers a afficher d'un utilisateur
    public function getUserDisplayedFiles($user, $firstRecordIndex, $maxRecord)
    {
    $queryBuilder = $this->createQueryBuilder('f');
    $queryBuilder->innerJoin('f.userFiles', 'uf', Expr\Join::WITH, $queryBuilder->expr()->eq('uf.email', '?1'));
    $queryBuilder->orderBy('f.name', 'ASC');
    $queryBuilder->setFirstResult($firstRecordIndex);
    $queryBuilder->setMaxResults($maxRecord);
    $queryBuilder->setParameter(1, $user->getEmail()); 

    $query = $queryBuilder->getQuery();
    $results = $query->getResult();
    return $results;
    }

    
    // Retourne le nombre de dossiers d'un utilisateur
    public function getUserFilesCount($user)
    {
    $queryBuilder = $this->createQueryBuilder('f');
    $queryBuilder->innerJoin('f.userFiles', 'uf', Expr\Join::WITH, $queryBuilder->expr()->eq('uf.email', '?1'));
    $queryBuilder->select($queryBuilder->expr()->count('f'));
    $queryBuilder->setParameter(1, $user->getEmail()); 

    $query = $queryBuilder->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }
    
    // Retourne le premier dossier d'un utilisateur (dans l'ordre d'affichage)
    public function getUserFirstFile($user)
    {
    $queryBuilder = $this->createQueryBuilder('f');
    $queryBuilder->innerJoin('f.userFiles', 'uf', Expr\Join::WITH, $queryBuilder->expr()->eq('uf.email', '?1'));
    $queryBuilder->orderBy('f.name', 'ASC');
    $queryBuilder->setMaxResults(1);
    $queryBuilder->setParameter(1, $user->getEmail()); 

    $query = $queryBuilder->getQuery();
    $results = $query->getOneOrNullResult();
    return $results;
    }
}
