<?php

namespace SD\CoreBundle\Repository;

/**
 * BookingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookingRepository extends \Doctrine\ORM\EntityRepository
{

	public function getBookings($file, \Datetime $date, $planification)
	{
	$qb = $this->createQueryBuilder('b');
    $qb->select('b.id bookingID');
    $qb->addSelect('bl.date date');

	$qb->where('b.file = :file')->setParameter('file', $file);
	$qb->andWhere('bl.planification = :planification')->setParameter('planification', $planification);

	$qb->innerJoin('b.bookingLines', 'bl');

	$qb->orderBy('bl.date', 'ASC');
	$qb->addOrderBy('b.id', 'ASC');

	$query = $qb->getQuery();
	$results = $query->getResult();
	return $results;
	}

}
