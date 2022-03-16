<?php
namespace Slub\SlubWebAddressbooks\Domain\Repository;

use \TYPO3\CMS\Extbase\Persistence\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alexander Bigga <typo3@slub-dresden.de>, SLUB Dresden
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

/**
 *
 *
 * @package slub_web_addressbooks
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AbstractRepository extends Repository
{

	/**
	 * Finds all datasets within a page range
	 *
	 * @param array $searchParams
	 * @return array The found Personnames
	 */
	public function findByPageRange($bookId, $startPage, $stopPage)
    {

		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->equals('book_id', $bookId);
		$constraints[] = $query->greaterThan('page', $startPage);
		$constraints[] = $query->lessThan('page', $stopPage);

		$query->matching($query->logicalAnd($constraints));

		$query->setOrderings(
			array('page' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		$result = $query->execute();

		return $result;
	}


	/**
	 * Finds all datasets fit with search criterias
	 *
	 * @param integer $bookId
	 * @return array The found Personnames
	 */
	public function findByBookId($bookId)
    {

		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->equals('book_id', $bookId);

		$query->matching($query->logicalAnd($constraints));

		$query->setOrderings(
			array('page' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
				'name' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

	/**
	 * Finds single dataset with given bookId
	 *
   * @param integer $image
	 * @param integer $bookId
	 * @return array The found Personnames
	 */
	public function findOneByImageAndBookId($image, $bookId)
    {

		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->equals('book_id', $bookId);
        $constraints[] = $query->equals('image', $image);

		$query->matching($query->logicalAnd($constraints));

		return $query->execute()->getFirst();
	}

}
