<?php
namespace Slub\SlubWebAddressbooks\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
class BookController extends AbstractController
{

	/**
	 * action list
	 *
	 * @param $currentYear
	 * @param $allYears
	 * @return void
	 */
	protected function getYearNav($currentYear, $allYears)
    {

		$years = array();

		foreach ($allYears as $id => $current) {
			if (!in_array($current->getYear(), $years))
				$years[] = $current->getYear();
		}

		// find id of current year:
		$currId = array_search($currentYear, $years);

		$info['yearFirst'] = $years[0];
		if ($currId-1 > 0)
			$info['yearPrevious'] = $years[$currId-1];
		else
			$info['yearPrevious'] = $info['yearFirst'];

		$info['yearLast'] = $years[count($years)-1];
		if ($currId+1 < count($years))
			$info['yearNext'] = $years[$currId+1];
		else
			$info['yearNext'] = $info['yearLast'];

		$this->view->assignMultiple(
			array(
				'yearFirst' => $info['yearFirst'],
				'yearLast' => $info['yearLast'],
				'yearPrevious' => $info['yearPrevious'],
				'yearNext' => $info['yearNext'],
				'yearCurrent' => $currentYear,
			)
		);

		return;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction()
    {
		// get parameter from timelinke as GET-parameter
		$year = $this->getParametersSafely('year');
		$placeId = $this->getParametersSafely('placeId');

		$allyears = $this->bookRepository->findByPlaceId($placeId);
		$book = $this->bookRepository->findByYearAndPlace($year, $placeId)->getFirst();
		$place = $this->placeRepository->findByUid($placeId);

		if (is_object($place)) {

			if ($book) {
				//$persons = $this->personRepository->findByBookId($book);
				$persons = $book->getPersons();

				$this->view->assign('persons', $persons);
				$this->view->assign('bookPersons', $book);

				$this->view->assign('pageBehoerdenverzeichnis', $book->getPageBehoerdenverzeichnis());
				$this->view->assign('PageBerufsklassenUndGewerbe', $book->getPageBerufsklassenUndGewerbe());
				$this->view->assign('PageHandelsregister', $book->getPageHandelsregister());
				$this->view->assign('PageGenossenschaftsregister', $book->getPageGenossenschaftsregister());
				$this->view->assign('LinkMap', $book->getLinkMap());
				$this->view->assign('LinkMapThumb', $book->getLinkMapThumb());
				$this->view->assign('pageHovLink', $place->getHovLink());

				$streets = $book->getStreets(); // $this->streetRepository->findByBookId($book);

				$this->view->assign('streets', $streets);
				$this->view->assign('bookStreets', $book);
			}
			$this->view->assign('place', $place);

			$this->getYearNav($year, $allyears);
		}
	}

	/**
	 * action search
	 *
	 * list persons and streets for a given year and place
	 *
	 * @return void
	 */
	public function searchAction()
    {

				$searchParams = $this->getParametersSafely('bookSearch');

				if (is_array($searchParams)) {
					// get parameter from search form
					$searchName = $searchParams['SearchString'];
					$year = $searchParams['year'];
					$placeId = $searchParams['placeId'];
				} else {
					// get parameter instead from GET variables
					$searchName = $this->getParametersSafely('SearchString');
					$year = $this->getParametersSafely('year');
					$placeId = $this->getParametersSafely('placeId');
				}
				$searchName = trim(str_replace(',', '', $searchName));

				if (empty($searchName) || $searchName == $GLOBALS['TSFE']->sL('LLL:EXT:slub_web_addressbooks/Resources/Private/Language/locallang.xlf:tx_slubwebaddressbooks_domain_model_book.search_field_label')) {

					$this->forward('list', NULL, NULL, array('year' => $year, 'placeId' => $placeId));

				}

				$allyears = $this->bookRepository->findByPlaceId($placeId);

				$book = $this->bookRepository->findByYearAndPlace($year, $placeId)->getFirst();

				$place = $this->placeRepository->findByUid($placeId);

				if ($book) {
					$this->view->assign('bookPersons', $book);
					$this->view->assign('closestSearchPersons', $this->findClosestNameFast($book, $searchName, $year, $book->getOrderUmlaute()['person'], 'person'));
					$this->view->assign('searchName', $searchName);
					$this->view->assign('pageBehoerdenverzeichnis', $book->getPageBehoerdenverzeichnis());
					$this->view->assign('PageBerufsklassenUndGewerbe', $book->getPageBerufsklassenUndGewerbe());
					$this->view->assign('PageHandelsregister', $book->getPageHandelsregister());
					$this->view->assign('PageGenossenschaftsregister', $book->getPageGenossenschaftsregister());
					$this->view->assign('pageHovLink', $place->getHovLink());

					if (isset($book->getOrderUmlaute()['street'])) {
						$this->view->assign('bookStreets', $book);
						$this->view->assign('closestSearchStreets', $this->findClosestNameFast($book, $searchName, $year, $book->getOrderUmlaute()['street'], 'street'));

						$this->view->assign('searchName', $searchName);
					}
			}

				$this->view->assign('place', $this->placeRepository->findByUid($placeId));

				$this->getYearNav($year, $allyears);
	}

	/**
	 * action searchcombined
	 *
	 * @return void
	 */
	public function searchcombinedAction()
    {

		$searchParams = $this->getParametersSafely('bookSearch');

		if (is_array($searchParams)) {
			// get parameter from search form
			$searchName = $searchParams['SearchString'];
			$year = $searchParams['year'];
			$placeId = $searchParams['placeId'];
		} else {
			// get parameter instead from GET variables
			$searchName = $this->getParametersSafely('SearchString');
			$year = $this->getParametersSafely('year');
			$placeId = $this->getParametersSafely('placeId');
		}
		// in case the plugin is set on a search result page an year is given,
		// we try to preselect the current year as option
		$this->searchStart($placeId, $year);
		$this->view->assign('searchName', $searchName);

	}

	/**
	 * action timeline
	 *
	 * @return void
	 */
	public function timelineAction()
    {

		$placeId = $this->getParametersSafely('placeId');

		// be sure that a place exists for placeId...
		$place = $this->placeRepository->findByUid($placeId);

		if (is_object($place)) {

			$books = $this->bookRepository->findByPlaceId($place);

            $timeline = [];

			foreach ($books as $book) {

				if (count($timeline) == 0) {
					$timeline[] = $book;
					continue;
				}
				$lastKey = $timeline[count($timeline)-1];

				if (is_object($lastKey)) {
					$lastYear = $lastKey->getYear();
				} else {
					$lastYear = $lastKey['year'];
				}

				for ($i = $lastYear + 1; $i < $book->getYear(); $i++) {
					$timeline[]['year'] = $i;
				}

				$timeline[] = $book;
			}

			$this->view->assign('books', $timeline);
		}
	}

	/**
	 * action guidedtour
	 *
	 * @return void
	 */
	public function guidedtourAction()
    {

		$searchParams = $this->getParametersSafely('bookSearch');
		if (is_array($searchParams)) {
			// get parameter from search form
			$year = $searchParams['year'];
			$placeId = $searchParams['placeId'];
		} else {
			// get parameter instead from GET variables
			$year = $this->getParametersSafely('year');
			$placeId = $this->getParametersSafely('placeId');
		}
		$action = $this->getParametersSafely('action');

		// set css class depending on navigation step
		switch ($action) {
			case 'timeline':
				$css['map'] = 'check';
				$css['timeline'] = 'active';
				$css['list'] = 'inactive';
				break;
			case 'list':
			case 'search':
				$css['map'] = 'check';
				$css['timeline'] = 'check';
				$css['list'] = 'active';
				break;
			default:
				$css['map'] = 'active';
				$css['timeline'] = 'inactive';
				$css['list'] = 'inactive';
				break;
		}

		$this->view->assign('year', $year);
		$this->view->assign('css', $css);
		$this->view->assign('placeId', $placeId);
	}

	/**
	 * Finds all datasets fit with search criterias
	 *
	 * @param Book $bookId
	 * @param string  $personName
	 * @param integer $year
	 * @param integer $umlautOrder
	 * @param string $type
	 * @return array The found Personnames
	 */
	private function findClosestNameFast($bookId, $personName, $year, $umlautOrder = 0, $type)
    {

		// simple made... TBD a better way...?
		// between 1910 and 1936 is different
		// exception: 1850, Personen, Dresden

		//~ 1	ä=a, ö=o, ü=u
		//~ 2	ä=ae, ö=oe, ü=ue
		//~ 3	ä hinter a aber vor b als eigener Buchstabe einsortiert
		if ($umlautOrder == 1 || ($umlautOrder == 0 && (($year >= 1910 && $year <= 1936) || ($year == 1850)))) {

			$umlaut_replace= array('a', 'A', 'o', 'o', 'O', 'u', 'U', 's', 'e', '', '', '', '', '', '');

		} else {

			$umlaut_replace= array('ae', 'Ae', 'oe', 'o', 'Oe', 'ue', 'Ue', 'ss', 'e', '', '', '', '', '', '');

		}

		$umlaute_source = array('ä', 'Ä', 'ö', 'ô', 'Ö', 'ü', 'Ü', 'ß', 'é', 'v.', 'von ', 'van ', 'd\'', '-', 'v.d.');

		$personName = trim(str_replace($umlaute_source, $umlaut_replace, $personName));

		$personNameLen = strlen($personName);


		$searchLen = max($personNameLen/2, 2);
		$searchName = substr($personName, 0, $searchLen);
		$persons = array();

		if (count($persons) == 0) {
			// ok, take them all - no matter of speed...
			// getting all is quite fast...
			switch ($type) {
				case 'person': $persons = $bookId->getPersons();
											break;
				case 'street': $persons = $bookId->getStreets();
											break;
			}

			// abort if only placeholder text exists in book
			if (count($persons) <= 1) {

				return;

			}
		}

		$nameId = 0;
		// find the name closest to search string
		// start from 'A' foreach to 'Z' and break if strcasecmp changes
		foreach ($persons as $id => $person) {

			// todo... replace depending on year...
			$nameCompare = trim(str_replace($umlaute_source, $umlaut_replace, $person['name']));

			// if we found a match, abort feach
			if (strncasecmp($personName, $nameCompare, $personNameLen+1) == 0) {
				$nameId = $id;
				break;
			} else if ( strncasecmp($personName, $nameCompare, $personNameLen) > 0) {
				// save id of predecessor for later getName
				$nameId = $id;
			} else // string compare is negative --> abort and give back last result
				break;

		}

		$name['name'] = $persons[$nameId]['name'];

		$name['image'] = $persons[$nameId]['image'];

		return $name;
	}

	/**
	 * Finds all datasets fit with search criterias
	 *
	 * @param array $searchParams
	 * @return array The found Personnames
	 */
	private function findAllByName($book, $personName)
    {

		$result = $book->getPersons();
		return;

		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->equals('book_id', $bookId);

		if (! empty($personName))
			$constraints[] = $query->like('name', $personName.'%');

		$query->matching($query->logicalAnd($constraints));

		$query->setOrderings(
			array('page' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING)
		);

		$result = $query->execute();

		return $result;
	}

}
