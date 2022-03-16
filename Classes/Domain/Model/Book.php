<?php
namespace Slub\SlubWebAddressbooks\Domain\Model;

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

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 *
 *
 * @package slub_web_addressbooks
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Book extends AbstractEntity
{

	/**
	 * Persons as String
	 *
	 * @var string
     * @Extbase\Validate("NotEmpty")
	 */
	protected $persons;

	/**
	 * Streets as String
	 *
	 * @var string
     * @Extbase\Validate("NotEmpty")
	 */
	protected $streets;


	/**
	 * Year as String
	 *
	 * @var string
     * @Extbase\Validate("NotEmpty")
	 */
	protected $yearString;

	/**
	 * Year of Publication
	 *
	 * @var integer
     * @Extbase\Validate("NotEmpty")
	 */
	protected $year;

	/**
	 * 1: Persons, 2:  Streets
	 *
	 * @var integer
     * @Extbase\Validate("NotEmpty")
	 */
	protected $type;

	/**
	 * ppn of the book
	 *
	 * @var string
	 */
	protected $ppn;

	/**
	 * Page of Behoerdenverzeichnis
	 *
	 * @var integer
	 */
	protected $pageBehoerdenverzeichnis;

	/**
	 * pageBerufsklassenUndGewerbe
	 *
	 * @var integer
	 */
	protected $pageBerufsklassenUndGewerbe;

	/**
	 * pageHandelsregister
	 *
	 * @var integer
	 */
	protected $pageHandelsregister;

	/**
	 * pageGenossenschaftsregister
	 *
	 * @var integer
	 */
	protected $pageGenossenschaftsregister;

	/**
	 * Link to Map in Deutschefotothek
	 *
	 * @var string
     * @Extbase\Validate("NotEmpty")
	 */
	protected $linkMap = '';

	/**
	 * Link to Thumb in Deutschefotothek
	 *
	 * @var string
     * @Extbase\Validate("NotEmpty")
	 */
	protected $linkMapThumb = '';

	/**
	 * Ordering of Umlaute
	 *
	 * @var string
	 */
	protected $orderUmlaute;

	/**
	 * Ordering of I and J at the beginning
	 *
	 * @var string
	 */
	protected $orderIJ;

	/**
	 * placeId
	 *
	 * @var \Slub\SlubWebAddressbooks\Domain\Model\Place
	 */
	protected $placeId;

	/**
	 * Returns the persons
	 *
	 * @return array $persons
	 */
	public function getPersons()
    {
		return unserialize($this->persons);
	}

	/**
	 * Sets the persons
	 *
	 * @param array $persons
	 * @return void
	 */
	public function setPersons($persons)
    {
		$this->persons = serialize($persons);
	}

	/**
	 * Returns the persons
	 *
	 * @return array $streets
	 */
	public function getStreets()
    {
		return unserialize($this->streets);
	}

	/**
	 * Sets the streets
	 *
	 * @param array $streets
	 * @return void
	 */
	public function setStreets($streets)
    {
		$this->streets = serialize($streets);
	}

	/**
	 * Returns the year
	 *
	 * @return integer $year
	 */
	public function getYear()
    {
		return $this->year;
	}

	/**
	 * Sets the year
	 *
	 * @param integer $year
	 * @return void
	 */
	public function setYear($year)
    {
		$this->year = $year;
	}

	/**
	 * Returns the ppn
	 *
	 * @return string $ppn
	 */
	public function getPpn()
    {
		return $this->ppn;
	}

	/**
	 * Sets the ppn
	 *
	 * @param string $ppn
	 * @return void
	 */
	public function setPpn($ppn)
    {
		$this->ppn = $ppn;
	}

	/**
	 * Returns the yearString
	 *
	 * @return string $yearString
	 */
	public function getYearString()
    {
		return $this->yearString;
	}

	/**
	 * Sets the yearString
	 *
	 * @param string $yearString
	 * @return void
	 */
	public function setYearString($yearString)
    {
		$this->yearString = $yearString;
	}

	/**
	 * Returns the placeId
	 *
	 * @return \Slub\SlubWebAddressbooks\Domain\Model\Place placeId
	 */
	public function getPlaceId()
    {
		return $this->placeId;
	}

	/**
	 * Sets the placeId
	 *
	 * @param \Slub\SlubWebAddressbooks\Domain\Model\Place $placeId
	 * @return \Slub\SlubWebAddressbooks\Domain\Model\Place placeId
	 */
	public function setPlaceId(\Slub\SlubWebAddressbooks\Domain\Model\Place $placeId)
    {
		$this->placeId = $placeId;
	}

	/**
	 * Returns the pageBehoerdenverzeichnis
	 *
	 * @return integer $pageBehoerdenverzeichnis
	 */
	public function getPageBehoerdenverzeichnis()
    {
		return $this->pageBehoerdenverzeichnis;
	}

	/**
	 * Sets the pageBehoerdenverzeichnis
	 *
	 * @param integer $pageBehoerdenverzeichnis
	 * @return void
	 */
	public function setPageBehoerdenverzeichnis($pageBehoerdenverzeichnis)
    {
		$this->pageBehoerdenverzeichnis = $pageBehoerdenverzeichnis;
	}

	/**
	 * Returns the pageHandelsregister
	 *
	 * @return integer $pageHandelsregister
	 */
	public function getPageHandelsregister()
    {
		return $this->pageHandelsregister;
	}

	/**
	 * Sets the pageHandelsregister
	 *
	 * @param integer $pageHandelsregister
	 * @return void
	 */
	public function setPageHandelsregister($pageHandelsregister)
    {
		$this->pageHandelsregister = $pageHandelsregister;
	}

	/**
	 * Returns the pageGenossenschaftsregister
	 *
	 * @return integer $pageGenossenschaftsregister
	 */
	public function getPageGenossenschaftsregister()
    {
		return $this->pageGenossenschaftsregister;
	}

	/**
	 * Sets the pageGenossenschaftsregister
	 *
	 * @param integer $pageGenossenschaftsregister
	 * @return void
	 */
	public function setPageGenossenschaftsregister($pageGenossenschaftsregister)
    {
		$this->pageGenossenschaftsregister = $pageGenossenschaftsregister;
	}

	/**
	 * Returns the linkMap
	 *
	 * @return string $linkMap
	 */
	public function getLinkMap()
    {
		return $this->linkMap;
	}

	/**
	 * Sets the linkMap
	 *
	 * @param string $linkMap
	 * @return void
	 */
	public function setLinkMap($linkMap)
    {
		$this->linkMap = $linkMap;
	}

	/**
	 * Returns the linkMapThumb
	 *
	 * @return string $linkMapThumb
	 */
	public function getLinkMapThumb()
    {
		return $this->linkMapThumb;
	}

	/**
	 * Sets the linkMapThumb
	 *
	 * @param string $linkMapThumb
	 * @return void
	 */
	public function setLinkMapThumb($linkMapThumb)
    {
		$this->linkMapThumb = $linkMapThumb;
	}

	/**
	 * Returns the pageBerufsklassenUndGewerbe
	 *
	 * @return integer pageBerufsklassenUndGewerbe
	 */
	public function getPageBerufsklassenUndGewerbe()
    {
		return $this->pageBerufsklassenUndGewerbe;
	}

	/**
	 * Sets the pageBerufsklassenUndGewerbe
	 *
	 * @param integer $pageBerufsklassenUndGewerbe
	 * @return integer pageBerufsklassenUndGewerbe
	 */
	public function setPageBerufsklassenUndGewerbe($pageBerufsklassenUndGewerbe)
    {
		$this->pageBerufsklassenUndGewerbe = $pageBerufsklassenUndGewerbe;
	}

	/**
	 * Returns the orderUmlaute
	 *
	 * @return array $orderUmlaute
	 */
	public function getOrderUmlaute()
    {
		return unserialize($this->orderUmlaute);
	}

	/**
	 * Sets the orderUmlaute
	 *
	 * @param array $orderUmlaute
	 * @return void
	 */
	public function setOrderUmlaute($orderUmlaute)
    {
		$this->orderUmlaute = serialize($orderUmlaute);
	}

	/**
	 * Returns the orderIJ
	 *
	 * @return string $orderIJ
	 */
	public function getOrderIJ()
    {
		return $this->orderIJ;
	}

	/**
	 * Sets the orderIJ
	 *
	 * @param string $orderIJ
	 * @return void
	 */
	public function setOrderIJ($orderIJ)
    {
		$this->orderIJ = $orderIJ;
	}

}
