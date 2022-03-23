<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2022 Alexander Bigga <typo3@slub-dresden.de>, SLUB Dresden
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
 * Commands to be executed by TYPO3, where the key of the array
 * is the name of the command (to be called as the first argument after "typo3").
 * Required parameter is the "class" of the command which needs to be a subclass
 * of \Symfony\Component\Console\Command\Command.
 *
 * This file is deprecated in TYPO3 v10 and will be removed in TYPO3 v11.
 * See Deprecation: #89139 - Console Commands configuration format Commands.php
 * https://docs.typo3.org/c/typo3/cms-core/master/en-us/Changelog/10.3/Deprecation-89139-ConsoleCommandsConfigurationFormatCommandsPhp.html
 */
return [
    'slubwebaddressbooks:import' => [
        'class' => Slub\SlubWebAddressbooks\Command\ImportCommand::class
    ],
];
