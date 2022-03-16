<?php
namespace Slub\SlubWebAddressbooks\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2016 Alexander Bigga <typo3@slub-dresden.de>, SLUB Dresden
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 *
 * @package slub_web_addressbooks
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BookRepository extends Repository
{

	/**
	 * defaultOrderings
	 *
	 * @var BookRepository
	 */
	protected $defaultOrderings = array("year"=> \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);

	/**
	 * Finds all datasets for a given place and year
	 *
	 * @param int $year
	 * @param int $placeId
	 * @return object The found Books
	 */
	public function findByYearAndPlace($year, $placeId)
    {

		$query = $this->createQuery();

		$constraints[] = $query->equals('place_id', $placeId);
		$constraints[] = $query->equals('year', $year);

		$query->matching($query->logicalAnd($constraints));

		$query->setOrderings(
			array('Year' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

	/**
	 * Finds all datasets for a given place and type
	 *
	 * @param string $ppn
	 * @param int $placeId
	 * @return object The found Books
	 */
	public function findOneByPpnAndPlaceId($ppn, $placeId)
    {

		$query = $this->createQuery();

		$constraints[] = $query->equals('place_id', $placeId);
		$constraints[] = $query->equals('ppn', $ppn);

		$query->matching($query->logicalAnd($constraints));

		$query->setOrderings(
			array('Year' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute()->getFirst();
	}



}
