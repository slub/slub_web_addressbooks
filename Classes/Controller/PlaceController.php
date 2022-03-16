<?php
namespace Slub\SlubWebAddressbooks\Controller;

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
class PlaceController extends AbstractController
{
    /**
	 * action list
	 * This is the map view
	 *
	 * @return void
	 */
	public function listAction()
    {
		$places = $this->placeRepository->findAll();
		$this->view->assign('places', $places);
	}

	/**
	 * action list
	 * This is the map view
	 *
	 * @return void
	 */
	public function geojsonAction()
    {

		$this->request->setFormat('html');

		$places = $this->placeRepository->findAll();

        $geoJson = [
            'type' => 'FeatureCollection'
        ];
        foreach ($places as $place) {
            $uri = $this->uriBuilder
                ->reset()
                ->setTargetPageUid($this->settings['pidTimeline'])
                ->setCreateAbsoluteUri(true)
                ->setArguments([
                    'tx_slubwebaddressbooks_booksearch[controller]' => 'Book',
                    'tx_slubwebaddressbooks_booksearch[action]' => 'timeline',
                    'tx_slubwebaddressbooks_booksearch[placeId]' => $place->getUid(),
                    ])
                ->build();

            $geoJson['features'][] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$place->getLon(), $place->getLat()],
                ],
                'properties' => [
                    'name' => $place->getPlace(),
                    'url' => $uri
                ]
            ];
        }
		$this->view->assign('geoJson', $geoJson);
	}
}
