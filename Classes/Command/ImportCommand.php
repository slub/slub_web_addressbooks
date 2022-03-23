<?php
namespace Slub\SlubWebAddressbooks\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <typo3@slub-dresden.de>, SLUB Dresden
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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Slub\SlubWebAddressbooks\Domain\Repository\BookRepository;
use Slub\SlubWebAddressbooks\Domain\Repository\PlaceRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;

 /**
  * ImportCommand
  */
 class ImportCommand extends Command
{

	/**
	 * bookRepository
	 *
	 * @var \Slub\SlubWebAddressbooks\Domain\Repository\BookRepository
	 */
	protected $bookRepository;

    /**
	 * placeRepository
	 *
	 * @var \Slub\SlubWebAddressbooks\Domain\Repository\PlaceRepository
	 */
	protected $placeRepository;

    /**
	 * @var boolean
	 */
	protected $dryRun;

    /**
	 * @var boolean
	 */
	protected $force;

    /**
	 * location URL of the METS file
	 *
	 * @var string
	 */
	protected $location;

    /**
     * @var int
     */
    protected $storagePid;

    /**
     * @var Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $io;

    /**
	 * Extbase objectManager
	 *
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

    /**
     * Configure the command by defining the name, options and arguments
     *
     * @return void
     */
    public function configure()
    {
        $this
            ->setDescription('Import Historical Addressbooks from Excel')
            ->setHelp('')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'If this option is set, the files will not actually be processed.'
            )
            ->addOption(
                'pid',
                'p',
                InputOption::VALUE_REQUIRED,
                'StoragePid of the indexed books.'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force indexing.'
            );
    }

    /**
     * Initialize the extbase repository based on the given storagePid.
     *
     * TYPO3 10+: Find a better solution e.g. based on Symfonie Dependancy Injection.
     *
     * @param int $storagePid The storage pid
     *
     * @return bool
     */
    protected function initializeRepositories($storagePid)
    {
        if (MathUtility::canBeInterpretedAsInteger($storagePid)) {
            $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
            $frameworkConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

            $frameworkConfiguration['persistence']['storagePid'] = MathUtility::forceIntegerInRange((int) $storagePid, 0);
            $configurationManager->setConfiguration($frameworkConfiguration);

            // TODO: When we drop support for TYPO3v9, we needn't/shouldn't use ObjectManager anymore
            $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

            $this->placeRepository = $this->objectManager->get(PlaceRepository::class);
            $this->bookRepository = $this->objectManager->get(BookRepository::class);
        } else {
            return false;
        }
        $this->storagePid = MathUtility::forceIntegerInRange((int) $storagePid, 0);

        return true;
    }

    /**
     * Executes the command to index the given document to db and solr.
     *
     * @param InputInterface $input The input parameters
     * @param OutputInterface $output The Symfony interface for outputs on console
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dryRun = $input->getOption('dry-run') != false ? true : false;
        $this->force = $input->getOption('force') != false ? true : false;

        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());

        $this->initializeRepositories($input->getOption('pid'));

        if ($this->storagePid == 0) {
            $this->io->error('ERROR: No valid PID (' . $this->storagePid . ') given.');
            exit(1);
        }

        $this->importAddressbooks();

        return 0;
    }

    /**
     * Import all Addressbooks
     *
     * @return void
     */
    protected function importAddressbooks()
    {

        $files = $this->getFiles();

        foreach ($files as $file) {
            $filename = $file->getName();
            if (stripos($filename, 'person') > 0) {
                if ($this->dryRun) {
                    $this->io->section('DRY RUN: Would index ' . $filename . ' (Persons) on PID ' . $this->storagePid . '.');
                } else {
                    $this->importSingleBook($file, 'person', $file->getProperty('modification_date'));
                }
            } else if (stripos($filename, 'strassen') > 0 || stripos($filename, 'straßen') > 0 ) {
                if ($this->dryRun) {
                    $this->io->section('DRY RUN: Would index ' . $filename . ' (Streets) on PID ' . $this->storagePid . '.');
                } else {
                    $this->importSingleBook($file, 'street', $file->getProperty('modification_date'));
                }
            }
        }
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
                if ($this->io->isVerbose()) {
                    $this->io->section('NEW: ' . $townName . '(' . $timestamp . ')');
                }
            } else {
                if (is_object($placeObj->getTstamp())) {
                    $timestampPlace = $placeObj->getTstamp()->getTimestamp();
                } else {
                    $timestampPlace = 0;
                }
                if ($timestampPlace > $timestamp && $this->force === false) {
                    if ($this->io->isVerbose()) {
                        $this->io->section('SKIP ' . $townName . ' as File is already indexed (' . $timestampPlace . '>' . $timestamp . ')');
                    }
                    return;
                } else {
                    // update activity
                    $update = true;
                    $placeObj->setTstamp(time());
                    if ($this->io->isVerbose()) {
                        $this->io->section('UPDATE ' . $townName . ' (' . $timestampPlace . '<' . $timestamp . ')');
                    }
                }
            }
            $placeObj->setGndid((string)$currentSheet->getCell('B3'));
            $placeObj->setHovLink((string)$currentSheet->getCell('C3'));

            // assure lat lon is written with "dot" not "comma"!
            $placeObj->setLat(number_format(str_replace(',', '.', (string)$currentSheet->getCell('D3')), 8));
            $placeObj->setLon(number_format(str_replace(',', '.', (string)$currentSheet->getCell('E3')), 8));

            if ($update) {
                $this->placeRepository->update($placeObj);
                if ($this->io->isVerbose()) {
                    $this->io->section('Updating place ' . $placeObj->getPlace() . '.');
                }
            } else {
                $this->placeRepository->add($placeObj);
                if ($this->io->isVerbose()) {
                    $this->io->section('Add place ' . $placeObj->getPlace() . '.');
                }
            }

            $this->doPersistAll();

          } else {

            $update = false;

            // reset on every loop
            $bookObj = null;
            $bookPpn = (string)$currentSheet->getCell('D2');

            // sometimes the PPN is missing - without it, we can't do anything
            if (!$placeObj || empty($bookPpn)) {
                $this->io->warning($absFileName . ': ABORT! (' . $sheetName . ') - no valid PPN given.');
                continue;
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
                if ($this->io->isVerbose()) {
                    $this->io->section('Create new Book object for PPN ' . $bookPpn . '.');
                }
                // set default values
                $bookObj->setPersons([]);
                $bookObj->setStreets([]);
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

                // get table of contents from METS structure
                $toc = $this->getTableOfContents($mets);

                if (!is_array($toc)) {
                    continue;
                }

                $toc = $this->normalizeToc($toc);

                $linkMap = $this->getLinkToMap($mets, $bookPpn);

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
                $this->io->warning($sheetName . ' nicht verfügbar. https://digital.slub-dresden.de/id' . $bookPpn);

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
                if ($this->io->isVerbose()) {
                    $this->io->section('Updating ' . $placeObj->getPlace() . ' ' . $type . ' ' . $bookObj->getYear() . '.');
                }
                $this->bookRepository->update($bookObj);
            } else {
                $this->bookRepository->add($bookObj);
                    if ($this->io->isVerbose()) {
                    $this->io->section('Add ' . $placeObj->getPlace() . ' ' . $type . ' ' . $bookObj->getYear() . '.');
                }
            }

            $placeObj->addBook($bookObj);

            $this->doPersistAll();
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
	 * Get Table of Contents from METS structure
	 *
     * @param simplexml $mets
	 * @return $array
	 */
	protected function getTableOfContents($mets)
    {

        // Get all logical units at top level.
        $divs = $mets->xpath('./mets:structMap[@TYPE="LOGICAL"]//mets:div');

        // Get structLinks (relation physical to logical IDs)
        $smLinks = $mets->xpath('./mets:structLink/mets:smLink');

        if (!empty($smLinks)) {
            foreach ($smLinks as $smLink) {
                $foundSmLinks['l2p'][(string) $smLink->attributes('http://www.w3.org/1999/xlink')->from][] = (string) $smLink->attributes('http://www.w3.org/1999/xlink')->to;
                $foundSmLinks['p2l'][(string) $smLink->attributes('http://www.w3.org/1999/xlink')->to][] = (string) $smLink->attributes('http://www.w3.org/1999/xlink')->from;
            }
        }

        // Get physical structure
        $physicalPages = $mets->xpath('./mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div[@TYPE="page"]');
        $log2Page = [];
        foreach ($physicalPages as $physPage) {
            $foundPhysPages[(string)$physPage['ID']] = (string)$physPage['ORDER'];
        }

        // Make a simple, unique array logical ID -> physical page number
        foreach ($foundSmLinks['l2p'] as $index => $log) {
            if (empty($log2Page[$index])) {
                $log2Page[$index] = $foundPhysPages[$log[0]];
            }
        }

        // Make the table of contents: LABEL -> physical page number
        $toc = [];
        foreach ($divs as $div) {
            if ((string)$div['LABEL']) {
                $toc[(string)$div['LABEL']] = $log2Page[(string)$div['ID']];
            }
        }

        return $toc;
    }


	/**
	 * Normalize table of contents and find entries for different writing of "Behördenverzeichnis" ec.
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

	/**
	 * fetch link to Map from METS/MODS and Fotothek
	 *
     * @param simplexml $mets
	 * @param string $bookPpn
	 * @return string
	 */
	protected function getLinkToMap($mets, $bookPpn)
    {

        $mets->registerXPathNamespace('mets', 'http://www.loc.gov/METS/');
        $mets->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');
        $mets->registerXPathNamespace('slub', 'http://slub-dresden.de/');

        $slubTarget = $mets->xpath('./mets:dmdSec[@ID="DMDLOG_0001"]/mets:mdWrap[@MDTYPE="MODS"]/mets:xmlData/mods:mods/mods:extension/slub:slub/slub:link/slub:target');

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

		return $linkMap;

	}

    /**
	 * Get the url of thumb-map
	 *
	 * @param $url
	 * @return string
	 */
	protected function getMapThumb($url)
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
