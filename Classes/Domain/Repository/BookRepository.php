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


	/**
	 * Check if given ppn is valid in tx_dlf_documents
	 *
	 * @param $ppn
	 * @return boolean
	 */
	public function getDlfUid($ppn)
    {

		$query = $this->createQuery();

		$buildquery = 'SELECT uid FROM tx_dlf_documents ';
		$buildquery .= 'WHERE purl = \'http://digital.slub-dresden.de/id' . $ppn . '\' ';
		$buildquery .= 'AND deleted = \'0\' ';
		$buildquery .= 'AND hidden = \'0\' ';

		$query->statement($buildquery);

		$result = $query->execute(TRUE);

		return $result[0];
	}

	/**
	 * fetch link to Map from METS/MODS and Fotothek
	 *
	 * @param string $bookPpn
	 * @return string
	 */
	public function getLinkToMap($bookPpn)
    {

        return;

		$doc = $this->initDlf($bookPpn);

		$slubTarget = $doc->mets->xpath('./mets:dmdSec[@ID="DMDLOG_0001"]/mets:mdWrap[@MDTYPE="MODS"]/mets:xmlData/mods:mods/mods:extension/slub:slub/slub:link/slub:target');

		$linkMap = array('thumb' => '', 'map' => '');

		if (is_array($slubTarget) && stripos((string)$slubTarget[0], 'fotothek') !== false) {

			// fetch the preview from fotothek
			$linkMap['thumb'] = $this->getMapThumb((string)$slubTarget[0]);

			// get basename of thumbnail image without suffix
			$baseNameImage = basename($linkMap['thumb'], '.jpg');

			// compose fotothek url...
			if (!empty($baseNameImage)) {

				$linkMap['map'] = 'http://www.deutschefotothek.de/db/apsisa.dll/ete?action=queryZoom/1&index=freitext&desc=' . $baseNameImage . '&medium=' . $baseNameImage;

			}

		}

		\tx_dlf_document::clearRegistry();

		return $linkMap;

	}

	/**
	 * fetch link to Behoerdenverzeichnis etc. from METS/MODS
	 *
	 * @param string $bookPpn
	 * @return array
	 */
	public function getLinkToc($bookPpn)
    {

		$toc = $this->searchLabels($doc->tableOfContents, $vals);

		\tx_dlf_document::clearRegistry();

		return $toc;

	}

	// recursive search for key in nested array
	// returns: array with "values" for the searched "key"

	/**
	 * searchLabels
	 *
	 * @param $array
	 * @param $vals
	 * @return
	 */
	public function searchLabels($array, &$vals)
    {

		foreach ($array as $key => $value) {

			if (is_array($value)) {

				$this->searchLabels($value, $vals);

			} else if ($key == 'label' && !empty($value) && \TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($array['points'])) {

				if (strpos($value, 'Behördenverzeichnis') !== false
					&& empty($vals['Behördenverzeichnis'])
				) {

					$vals['Behördenverzeichnis'] = $array['points'];

				} elseif ($value == "Berufsklassen und Gewerbebetriebe" ||
					(strpos($value, 'Berufsklassen') !== false && strpos($value, 'Gewerbe') !== false
						&& empty($vals['BerufsklassenGewerbeverzeichnis']))
				) {

					$vals['BerufsklassenGewerbeverzeichnis'] = $array['points'];

				} elseif (strpos($value, 'Handelsregister') !== false
					&& empty($vals['Handelsregister'])
				) {

					$vals['Handelsregister'] = $array['points'];

				} elseif (strpos($value, 'Genossenschaftsregister') !== false
					&& empty($vals['Genossenschaftsregister'])
				) {

					$vals['Genossenschaftsregister'] = $array['points'];

				}
			}
		}

		return $vals;
	}

	/**
	 * Geht the url of thumb-map
	 *
	 * @param $url
	 * @return string
	 */
	private function getMapThumb($url)
    {

		$p = xml_parser_create();

		$parseret = xml_parse_into_struct($p, @file_get_contents($url), $vals);

		xml_parser_free($p);

		foreach ($vals as $id => $value) {
			// the thumb images have always the "onerror" tag set...
			if ($value['tag'] == 'IMG' && !empty($value['attributes']['ONERROR'])) {
				if (preg_match('/\.jpg$/', $value['attributes']['SRC'])) {
					$getHeaders = get_headers($value['attributes']['SRC'], 1);
					if (strstr($getHeaders[0], '200') !== false) {
						$thumb = $value['attributes']['SRC'];
						break; // we take the first one...
					}
				}
			}
		}

		return $thumb;
	}
}
