<?php
namespace Slub\SlubWebAddressbooks\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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

use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Slub\SlubWebAddressbooks\Domain\Repository\BookRepository;
use Slub\SlubWebAddressbooks\Domain\Repository\PlaceRepository;

 /**
  * ImportCommandController
  */
 class ImportCommandController extends CommandController
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
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * injectConfigurationManager
     *
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
	 * location URL of the METS file
	 *
	 * @var string
	 */
	protected $location;

    /**
     * Init some settings like storagePid
     *
     * @param int $storagePid
     *
     * @return void
     */
    protected function init($storagePid = -1)
    {

       // if there is no storagePid as CLI parameter, take it from extension configuration
       if (!MathUtility::canBeInterpretedAsInteger($storagePid) || ($storagePid < 0) ) {

           // abort if no storagePid is found
           echo 'NO valid storagePid given. Please enter the storagePid.' . $storagePid . '.' . "\n";
           exit(1);

       }

       // set storagePid to point extbase to the right repositories
       $configurationArray = [
           'persistence' => [
               'storagePid' => $storagePid,
           ],
       ];
       $this->configurationManager->setConfiguration($configurationArray);

    }

    /**
     * Import all Addressbooks
     *
     *
     * @param int    storagePid
     *
     * @return void
     */
    public function importAddressbooksCommand($storagePid = -1)
    {

        $this->init($storagePid);

        $files = $this->getFiles();

        foreach ($files as $file) {

        $filename = $file->getName();
        if (stripos($filename, 'person') > 0) {

            $this->importSingleBook($file, 'person', $file->getProperty('modification_date'));

        } else if (stripos($filename, 'strassen') > 0 || stripos($filename, 'straßen') > 0 ) {

            $this->importSingleBook($file, 'street', $file->getProperty('modification_date'));

        }

        echo "\n";

        }

        echo "All Done :-)\n";

    }

    /**
     * Import single Personenverzeichnis
     *
     * @param File $file
     * @param string $type
     * @param int $timestamp
     * @return array
     */
    private function importSingleBook($file, $type, $timestamp)
    {

        $absFileName = GeneralUtility::getFileAbsFileName('fileadmin' . $file->getIdentifier());

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($absFileName);
        $reader->setReadDataOnly(TRUE);

        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($absFileName);

        $allSheetNames = $spreadsheet->getSheetNames();

        // by convention, the first sheetname is called "Bemerkungen", all others contain the years
        foreach ($allSheetNames as $sheetName) {

          $currentSheet = $spreadsheet->getSheetByName($sheetName);

          $sheetName = trim($sheetName);

          if ($sheetName == "Bemerkungen") {

            $update = false;

            $townName = (string)$currentSheet->getCell('A3');

            $placeObj = $this->placeRepository->findOneByPlace($townName);

            if (!$placeObj) {
                $placeObj = $this->objectManager->get(\Slub\SlubWebAddressbooks\Domain\Model\Place::class);
                $placeObj->setPlace($townName);
                echo "NEW: " . $townName . '(' . $timestamp . ')' . "\n";
            } else {
                if (is_object($placeObj->getTstamp())) {
                    $timestampPlace = $placeObj->getTstamp()->getTimestamp();
                } else {
                    $timestampPlace = 0;
                }
                if ($timestampPlace > $timestamp) {
                    echo "SKIP " . $townName . ' as File is already indexed (' . $timestampPlace . '>' . $timestamp . ')' . "\n";
                    return;
                } else {
                    // update activity
                    $update = true;
                    $placeObj->setTstamp(time());
                    echo "UPDATE " . $townName . ' (' . $timestampPlace . '<' . $timestamp . ')' . "\n";
                }
            }
            $placeObj->setGndid((string)$currentSheet->getCell('B3'));
            $placeObj->setHovLink((string)$currentSheet->getCell('C3'));

            // assure lat lon is written with "dot" not "comma"!
            $placeObj->setLat(number_format(str_replace(',', '.', (string)$currentSheet->getCell('D3')), 8));
            $placeObj->setLon(number_format(str_replace(',', '.', (string)$currentSheet->getCell('E3')), 8));

            if ($update) {
                $this->placeRepository->update($placeObj);
            } else {
                $this->placeRepository->add($placeObj);
            }

            $this->doPersistAll();

            echo $townName . ' (' . $type . ') ';

          } else {

            $update = false;

            $bookPpn = (string)$currentSheet->getCell('D2');

            // sometimes the PPN is missing - without it, we can't do anything
            if (!$placeObj || empty($bookPpn)) {
                echo $absFileName . ': ABORT! (' . $sheetName . ')';
                continue;
            } else {
                  echo $sheetName . ', ';
            }

            if (strpos($sheetName, '-') === FALSE) {
                $year = (int)$sheetName;
            } else {
                $year = substr($sheetName, 0, strpos($sheetName, '-'));
            }

            $bookObj = $this->bookRepository->findOneByPpnAndPlaceId($bookPpn, $placeObj);

            if (!$bookObj) {
                $bookObj = $this->objectManager->get(\Slub\SlubWebAddressbooks\Domain\Model\Book::class);
                $bookObj->setPpn($bookPpn);
            } else {
                // update activity
                $update = true;
            }

            $bookObj->setYearString($sheetName);
            $bookObj->setYear($year);
            $bookObj->setPlaceId($placeObj);

            $orderUmlaute = $bookObj->getOrderUmlaute();
            $orderUmlaute[$type] = (string)$currentSheet->getCell('E2');
            $bookObj->setOrderUmlaute($orderUmlaute);

            $bookObj->setOrderIJ((string)$currentSheet->getCell('F2'));

            // try to get the location URL from Solr for given PPN
            $is_available = $this->getLocationFromPpn($bookPpn);

            if ($is_available !== false) {

                $mets = $this->getMets($this->location);

                if ($mets === false) {
                    continue;
                }

                // Get all logical units at top level.
                $divs = $mets->xpath('./mets:structMap[@TYPE="LOGICAL"]//mets:div');

                $smLinks = $this->getSmLinks($mets);

                $toc = [];
                foreach ($divs as $div) {
                    if ((string)$div['LABEL']) {
                        $toc[(string)$div['LABEL']] = $smLinks[(string)$div['ID']];
                    }
                }

                $toc = $this->normalizeToc($toc);

              $linkMap = $this->bookRepository->getLinkToMap($bookPpn);

              if (!empty($linkMap['thumb'])) {
                $bookObj->setLinkMapThumb($linkMap['thumb']);
              }
              if (!empty($linkMap['map'])) {
                $bookObj->setLinkMap($linkMap['map']);
              }

            if (!empty($toc['Behördenverzeichnis'])) {
          			$bookObj->setPageBehoerdenverzeichnis($toc['Behördenverzeichnis']);
              }
          		if (!empty($toc['BerufsklassenGewerbeverzeichnis'])) {
          			$bookObj->setPageBerufsklassenUndGewerbe($toc['BerufsklassenGewerbeverzeichnis']);
              }
          		if (!empty($toc['Handelsregister'])) {
          			$bookObj->setPageHandelsregister($toc['Handelsregister']);
              }
          		if (!empty($toc['Genossenschaftsregister'])) {
          			$bookObj->setPageGenossenschaftsregister($toc['Genossenschaftsregister']);
              }

            } else {

                unset($bookObj);
                echo $sheetName . " (nicht verfügbar), ";

                continue;
            }

            $allNames = array();

            foreach ($currentSheet->getRowIterator() as $row) {

              if ($row->getRowIndex()< 2) {
                continue;
              }

              $breakRowIteration = FALSE;

              $name = array();

              $cellIterator = $row->getCellIterator();

              foreach ($cellIterator as $cell) {

                switch ($cell->getColumn()) {
                  case 'A': //echo "cell A: ". $cell->getValue() . "\n";
                            if (empty($cell->getValue())) {
                              $breakRowIteration = TRUE;
                            } else {
                              $name['name'] = $cell->getValue();
                            }
                            break;
                  case 'B': //echo "cell B: ". $cell->getValue() . "\n";
                            $name['image'] = $cell->getValue();
                            break;
                  case 'C': //echo "cell C: ". $cell->getValue() . "\n";
                            $name['page'] = $cell->getValue();
                            break;
                  default: break;
                }

              }

              $allNames[] = $name;

              if ($breakRowIteration === TRUE) {
                break;
              }

              switch ($type) {
                case 'person':
                    $bookObj->setPersons($allNames);
                    break;
                case 'street':
                    $bookObj->setStreets($allNames);
                    break;
              }

            }

            if ($update) {
              $this->bookRepository->update($bookObj);
            } else {
              $this->bookRepository->add($bookObj);
            }

          }

        }

    }

    /**
     * Action addImage
     *
     * @return array
     */
    private function getFiles()
    {
      $storageRepository = $this->objectManager->get(\TYPO3\CMS\Core\Resource\StorageRepository::class);
      $storage = $storageRepository->findByUid('1');
      // TODO: make configurable
      $files = $storage->getFilesInFolder($storage->getFolder('groups/adressbuecher/excel/'));
      return $files;
    }

    /**
     * Persist All Changes upto now.
     *
     * @return void
     */
    protected function doPersistAll()
    {

        $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);

        $persistenceManager->persistAll();

    }

    /**
     * Get the location URL of the METS file for the given PPN
     * @param string ppn
     * @return boolean
     */
    protected function getLocationFromPpn($Ppn)
    {
        $location = '';
        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $configuration = [
            'timeout' => 10,
//            'timeout' => $this->settings['solr']['timeout'],
            'headers' => [
                'Content-type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'
            ],
        ];
        $configuration['form_params'] = [
            'q' => 'record_id:"oai:de:slub-dresden:db:id-' . $Ppn . '"',
            'fq' => 'toplevel:true',
            'fl' => 'location',
            'rows' => 1,
            'wt' => 'json',
            'json.nl' => 'flat',
            'omitHeader' => 'true'
        ];
        $response = $requestFactory->request('http://sdvsolrslub.slub-dresden.de:8983/solr/dlfCore0/select?', 'POST', $configuration);
//        $response = $requestFactory->request($this->settings['solr']['host'] . '/select?', 'POST', $configuration);
        $content  = $response->getBody()->getContents();
        $result = json_decode($content, true);
        if ($result) {
            foreach ($result['response']['docs'] as $doc) {
                $location = $doc['location'];
            }
        }

        if ($location) {
            $this->location = $location;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $location
     * @return string|boolean METS file
     */
    private function getMets($location)
    {
        $content = GeneralUtility::getUrl($location);
        if ($content !== false) {
            $xml = simplexml_load_string($content);
        } else {
            return false;
        }
        return $xml;
    }

    /**
     * @param simplexml $mets
     */
    protected function getSmLinks($mets)
    {
        $smLinks = $mets->xpath('./mets:structLink/mets:smLink');
        if (!empty($smLinks)) {
            foreach ($smLinks as $smLink) {
                $foundSmLinks['l2p'][(string) $smLink->attributes('http://www.w3.org/1999/xlink')->from][] = (string) $smLink->attributes('http://www.w3.org/1999/xlink')->to;
                $foundSmLinks['p2l'][(string) $smLink->attributes('http://www.w3.org/1999/xlink')->to][] = (string) $smLink->attributes('http://www.w3.org/1999/xlink')->from;
            }
        }

        $physicalPages = $mets->xpath('./mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div[@TYPE="page"]');
        $log2Page = [];
        foreach ($physicalPages as $physPage) {
            $foundPhysPages[(string)$physPage['ID']] = (string)$physPage['ORDER'];
        }

        foreach ( $foundSmLinks['l2p'] as $index => $log) {
            if (empty($log2Page[$index])) {
                $log2Page[$index] = $foundPhysPages[$log[0]];
            }
        }

        return $log2Page;
    }


	/**
	 * normalizeToc
	 *
	 * @param $array
	 * @return $array
	 */
	protected function normalizeToc($toc)
    {

        $nToc = [];
		foreach ($toc as $label => $page) {

				if (strpos($label, 'Behördenverzeichnis') !== false
					&& empty($nToc['Behördenverzeichnis'])
				) {

					$nToc['Behördenverzeichnis'] = $page;

				} elseif ($label == "Berufsklassen und Gewerbebetriebe" ||
					(strpos($label, 'Berufsklassen') !== false && strpos($label, 'Gewerbe') !== false
						&& empty($nToc['BerufsklassenGewerbeverzeichnis']))
				) {

					$nToc['BerufsklassenGewerbeverzeichnis'] = $page;

				} elseif (strpos($label, 'Handelsregister') !== false
					&& empty($nToc['Handelsregister'])
				) {

					$nToc['Handelsregister'] = $page;

				} elseif (strpos($label, 'Genossenschaftsregister') !== false
					&& empty($nToc['Genossenschaftsregister'])
				) {

					$nToc['Genossenschaftsregister'] = $page;

				}
        }

		return $nToc;
	}


}
