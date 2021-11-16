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

            $isAvailable = $this->bookRepository->getBookPurl((string)$currentSheet->getCell('D2'))['is_available'];

            if ($isAvailable == '1') {

              $linkMap = $this->bookRepository->getLinkToMap($bookPpn);

              if (!empty($linkMap['thumb'])) {
                $bookObj->setLinkMapThumb($linkMap['thumb']);
              }
              if (!empty($linkMap['map'])) {
                $bookObj->setLinkMap($linkMap['map']);
              }

              $toc = $this->bookRepository->getLinkToc($bookPpn);

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
                case 'person' :   $bookObj->setPersons($allNames);
                              break;
                case 'street' :   $bookObj->setStreets($allNames);
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

}
