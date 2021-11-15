# SLUB Addressbooks Extensions

SitePackage for the SLUB Historical Addressbooks

## Import Addressbooks by Scheduler

* Create Scheduler Task
* Extbase Command Controller
* add storagePid

## Import Folder

This is hard-coded in ImportcommandController.php

fileadmin/groups/adressbuecher/excel/

## Dependancy to Kitodo.Presentation

A local Kitodo.Presentation instance has been used to verify if a certain PPN is available. Unfortunately this doesn't work anymore after Upgrade to TYPO3 9.5 as the Digitale Sammlungen are not on the same system anymore.

To check the availability, it's easy to ask the Solr-Server.

But how do we get the "Behördenverzeichnis" (Table Of Contents) etc.? This can only be done by parsing the METS by ourself.
