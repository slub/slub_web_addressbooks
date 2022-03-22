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
class Place extends AbstractEntity
{

	/**
	 * Cityname
	 *
     * @Extbase\Validate("NotEmpty")
	 * @var string
	 */
	protected $place;

	/**
	 * Latitude
	 *
	 * @var string
	 */
	protected $lat;

	/**
	 * Longitude
	 *
	 * @var string
	 */
	protected $lon;

	/**
	 * Link to Historisches Ortsverzeichnis Sachsen
	 *
	 * @var string
	 */
	protected $hovLink;

	/**
	 * GND-ID
	 *
	 * @var string
	 */
	protected $gndid;

	/**
	 * tstamp
	 *
	 * @var \DateTime
	 */
	protected $tstamp;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubWebAddressbooks\Domain\Model\Book>
     * @Extbase\ORM\Lazy
     */
    protected $books = null;

    /**
	 * Returns tstamp timestamp
	 *
	 * @return \DateTime
	 */
	public function getTstamp()
    {
		return $this->tstamp;
	}

	/**
	 * @param $tstamp
	 * @return void
	 */
	public function setTstamp($tstamp)
    {
		$this->tstamp = $tstamp;
	}

	/**
	 * Returns the place
	 *
	 * @return string $place
	 */
	public function getPlace()
    {
		return $this->place;
	}

	/**
	 * Sets the place
	 *
	 * @param string $place
	 * @return void
	 */
	public function setPlace($place)
    {
		$this->place = $place;
	}

	/**
	 * Returns the lat
	 *
	 * @return string $lat
	 */
	public function getLat()
    {
		return $this->lat;
	}

	/**
	 * Sets the lat
	 *
	 * @param string $lat
	 * @return void
	 */
	public function setLat($lat)
    {
		$this->lat = $lat;
	}

	/**
	 * Returns the lon
	 *
	 * @return string $lon
	 */
	public function getLon()
    {
		return $this->lon;
	}

	/**
	 * Sets the lon
	 *
	 * @param string $lon
	 * @return void
	 */
	public function setLon($lon)
    {
		$this->lon = $lon;
	}

	/**
	 * Returns the hovLink
	 *
	 * @return string $hovLink
	 */
	public function getHovLink()
    {
		return $this->hovLink;
	}

	/**
	 * Sets the hovLink
	 *
	 * @param string $hovLink
	 * @return void
	 */
	public function setHovLink($hovLink)
    {
		$this->hovLink = $hovLink;
	}

	/**
	 * Returns the gndid
	 *
	 * @return string $gndid
	 */
	public function getGndid()
    {
		return $this->gndid;
	}

	/**
	 * Sets the gndid
	 *
	 * @param string $gndid
	 * @return void
	 */
	public function setGndid($gndid)
    {
		$this->gndid = $gndid;
	}

}
