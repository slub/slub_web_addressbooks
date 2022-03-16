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

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Slub\SlubWebAddressbooks\Domain\Repository\BookRepository;
use Slub\SlubWebAddressbooks\Domain\Repository\PlaceRepository;

/**
 *
 *
 * @package slub_web_addressbooks
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AbstractController extends ActionController
{

	/**
	 * bookRepository
	 *
	 * @var \Slub\SlubWebAddressbooks\Domain\Repository\BookRepository
	 */
	protected $bookRepository;

	/**
     * @param \Slub\SlubWebAddressbooks\Domain\Repository\BookRepository $bookRepository
     */
    public function injectBookRepository(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
	 * placeRepository
	 *
	 * @var \Slub\SlubWebAddressbooks\Domain\Repository\PlaceRepository
	 */
	protected $placeRepository;

	/**
     * @param \Slub\SlubWebAddressbooks\Domain\Repository\PlaceRepository $placeRepository
     */
    public function injectPlaceepository(PlaceRepository $placeRepository)
    {
        $this->placeRepository = $placeRepository;
    }

    /**
	* @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	*/
	protected $configurationManager;

 	/**
	 * @var Content Object
	 */
	protected $contentObj;

    /**
	 * Target PID of form actions
	 *
	 * @var integer
     * @Extbase\Validate("NotEmpty")
	 */
	protected $actionPid;

	/**
	 * injectConfigurationManager
	 *
	 * we overwrite the injectConfigurationManager from extbase:Classes/MVC/Controller/AbstractController.php
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
    {
		$this->configurationManager = $configurationManager;
		$this->contentObj = $this->configurationManager->getContentObject();
		$this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);

		if (!empty($this->settings['pidTimeline']))
				$this->actionPid = $this->settings['pidTimeline'];
			else {
				$this->actionPid = $this->contentObj->data['pid'];
		}
	}

	/**
	 * helper function to translate traditional sorting
	 *
	 * @param string $string
	 * @param int $year
	 * @return string
	 */
	protected function replaceStringForTradSorting($string, $year)
    {
        return $string;
	}

	/**
	 * action search: Shows a search form.
	 *
	 * @return void
	 */
	public function searchStart($placeId, $selectedYear)
    {

		// placeId = 1: Dresden is default preselected
		if (empty($placeId))
			$placeId = 1;

		$places = $this->placeRepository->findAll();

		// values get updated by ajax call
		$books = $this->bookRepository->findByPlaceId($placeId);

		$this->view->assignMultiple(
			array(
				'years' => $books,
				'places' => $places,
				'selectedPlaceId' => $placeId,
				'selectedYear' => $selectedYear,
			)
		);
	}

	/**
	 * Safely gets Parameters from request
	 * if they exist
	 *
	 * @param string $parameterName
	 * @return
	 */
	protected function getParametersSafely($parameterName)
    {

		if($this->request->hasArgument( $parameterName )){
			return $this->request->getArgument( $parameterName );
		}
		return NULL;
	}

}
