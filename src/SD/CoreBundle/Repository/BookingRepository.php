<?php

namespace SD\CoreBundle\Repository;

use Doctrine\ORM\Query\Expr;
use SD\CoreBundle\DQL\DateFormatFunctionDQL;

/**
 * BookingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class BookingRepository extends \Doctrine\ORM\EntityRepository
{
	// Affichage des réservations dans la grille horaire journalière
	public function getTimetableBookings($file, \Datetime $date, $planification, $planificationPeriod)
	{
	$qb = $this->createQueryBuilder('b');
    $qb->select('b.id bookingID');
	$qb->addSelect('bl.date date');

    $qb->addSelect('p.id planificationID');
    $qb->addSelect('pp.id planificationPeriodID');
	$qb->addSelect('pl.id planificationLineID');
	$qb->addSelect('r.id resourceID');
	$qb->addSelect('t.id timetableID');
	$qb->addSelect('tl.id timetableLineID');

	$qb->where('b.file = :file')->setParameter('file', $file);
	$qb->andWhere("DATE_FORMAT(bl.date,'%Y%m%d') = :date")->setParameter('date', $date->format('Ymd'));
	$qb->andWhere('bl.planification = :planification')->setParameter('planification', $planification);
	$qb->andWhere('bl.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);

	$qb->innerJoin('b.bookingLines', 'bl');
	$qb->innerJoin('bl.planification', 'p');
	$qb->innerJoin('bl.planificationPeriod', 'pp');
	$qb->innerJoin('bl.planificationLine', 'pl');
	$qb->innerJoin('bl.resource', 'r');
	$qb->innerJoin('bl.timetable', 't');
	$qb->innerJoin('bl.timetableLine', 'tl');

	$qb->orderBy('bl.date', 'ASC');
	$qb->addOrderBy('p.id', 'ASC');
	$qb->addOrderBy('pp.id', 'ASC');
	$qb->addOrderBy('pl.id', 'ASC');
	$qb->addOrderBy('r.id', 'ASC');
	$qb->addOrderBy('t.id', 'ASC');
	$qb->addOrderBy('tl.id', 'ASC');

	$query = $qb->getQuery();
	$results = $query->getResult();
	return $results;
	}

	// Affichage des réservations dans le calendrier
	public function getCalendarBookings($file, $planification)
    {
    $qb = $this->createQueryBuilder('b');
    $qb->where('b.file = :file')->setParameter('file', $file);
	$qb->andWhere('b.planification = :planification')->setParameter('planification', $planification);
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }

	// Toutes les réservations d'un dossier
	public function getAllBookingsCount($file)
    {
    $qb = $this->createQueryBuilder('b');
	$qb->select($qb->expr()->count('b'));
    $qb->where('b.file = :file')->setParameter('file', $file);
    $query = $qb->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }

	public function getAllBookings($file, $firstRecordIndex, $maxRecord)
    {
    $qb = $this->createQueryBuilder('b');
    $qb->where('b.file = :file')->setParameter('file', $file);
    $qb->orderBy('b.beginningDate', 'ASC');
    $qb->setFirstResult($firstRecordIndex);
    $qb->setMaxResults($maxRecord);
  
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }

	// Les réservations d'un dossier au delà d'une date
	public function getFromDatetimeBookingsCount($file, \Datetime $dateTime)
	{
	$qb = $this->createQueryBuilder('b');
	$qb->select($qb->expr()->count('b'));
	$qb->where('b.file = :file')->setParameter('file', $file);
	$qb->andWhere("DATE_FORMAT(b.beginningDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));
	$query = $qb->getQuery();
	$singleScalar = $query->getSingleScalarResult();
	return $singleScalar;
	}

	public function getFromDatetimeBookings($file, \Datetime $dateTime, $firstRecordIndex, $maxRecord)
    {
    $qb = $this->createQueryBuilder('b');
    $qb->where('b.file = :file')->setParameter('file', $file);
	$qb->andWhere("DATE_FORMAT(b.beginningDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));
    $qb->orderBy('b.beginningDate', 'ASC');
    $qb->setFirstResult($firstRecordIndex);
    $qb->setMaxResults($maxRecord);
  
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }
	
	// Les réservations d'un dossier et d'un utilisateur
	public function getUserFileBookingsCount($file, $userFile)
	{
	$qb = $this->createQueryBuilder('b');
	$qb->select($qb->expr()->count('b'));
	$qb->where('b.file = :file')->setParameter('file', $file);
	
	$this->getBookingUser($qb);
	$this->getBookingUserParameter($qb, $userFile);

	$query = $qb->getQuery();
	$singleScalar = $query->getSingleScalarResult();
	return $singleScalar;
	}

	public function getUserFileBookings($file, $userFile, $firstRecordIndex, $maxRecord)
	{
    $qb = $this->createQueryBuilder('b');
    $qb->where('b.file = :file')->setParameter('file', $file);

	$this->getBookingUser($qb);
	$this->getBookingUserParameter($qb, $userFile);

    $qb->orderBy('b.beginningDate', 'ASC');
    $qb->setFirstResult($firstRecordIndex);
    $qb->setMaxResults($maxRecord);
  
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
	}

	// Les réservations d'un dossier et d'un utilisateur au delà d'une date
	public function getUserFileFromDatetimeBookingsCount($file, $userFile, \Datetime $dateTime)
	{
	$qb = $this->createQueryBuilder('b');
	$qb->select($qb->expr()->count('b'));
	$qb->where('b.file = :file')->setParameter('file', $file);
	$qb->andWhere("DATE_FORMAT(b.beginningDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));
	
	$this->getBookingUser($qb);
	$this->getBookingUserParameter($qb, $userFile);

	$query = $qb->getQuery();
	$singleScalar = $query->getSingleScalarResult();
	return $singleScalar;
	}

	public function getUserFileFromDatetimeBookings($file, $userFile, \Datetime $dateTime, $firstRecordIndex, $maxRecord)
	{
    $qb = $this->createQueryBuilder('b');
    $qb->where('b.file = :file')->setParameter('file', $file);
	$qb->andWhere("DATE_FORMAT(b.beginningDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));

	$this->getBookingUser($qb);
	$this->getBookingUserParameter($qb, $userFile);

    $qb->orderBy('b.beginningDate', 'ASC');
    $qb->setFirstResult($firstRecordIndex);
    $qb->setMaxResults($maxRecord);
  
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
	}

	// Utilisateur de réservation
	public function getBookingUser($qb)
	{
	$qb->innerJoin('b.bookingUserFiles', 'bu', Expr\Join::WITH, $qb->expr()->eq('bu.userFile', ':userFile'));
	}

	public function getBookingUserParameter($qb, $userFile)
    {
	$qb->setParameter('userFile', $userFile);
    }
}
