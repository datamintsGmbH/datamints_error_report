<?php

namespace Datamints\DatamintsErrorReport\Command\Report;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Mark Weisgerber <m.weisgerber@datamints.com>, datamints GmbH
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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Belog\Domain\Model\Constraint;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 *
 * @package datamints_elearning
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SendCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected InputInterface $input;
    /**
     * @var \TYPO3\CMS\Core\Registry
     */
    protected \TYPO3\CMS\Core\Registry $registry;

    /**
     * @var \Datamints\DatamintsErrorReport\Services\MailService
     */
    protected \Datamints\DatamintsErrorReport\Services\MailService $mailService;

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure (): void
    {
        $this->setDescription('Verschickt einen gebündelten Fehlerbericht der seit dem letzten Aufruf gesammelten Fehlern');

        $this->addOption(
            'max',
            'm',
            InputOption::VALUE_REQUIRED,
            'Maximale Anzahl der gleichzeitig verschickten Fehlermeldungen pro Mail (Schutz vor zu großen Mails)',
            '50'
        );

        $this->addOption(
            'name',
            'n',
            InputOption::VALUE_REQUIRED,
            'Name des Systems',
            'Vorlage'
        );

        $this->addOption(
            'recipient',
            'r',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'E-Mail Adresse des Empfängers. Mehrere können definiert werden, indem --recipient mehrmals spezifiert wird.',
            ['m.weisgerber@datamints.com']
        );
    }

    /**
     * Verschickt einen gebündelten Fehlerbericht der seit dem letzten Aufruf gesammelten Fehlern
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int error code
     */
    protected function execute (InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->initializeDependencies();


        // Logs beziehen
        $logRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Belog\Domain\Repository\LogEntryRepository::class);
        $logs = $logRepository->findByConstraint($this->getConstraint());

        // Wenn keine Logs gefunden wurden, brauchen wir nicht weiter machen
        if (count($logs) == 0) {
            return 0;
        }

        // Filtern nach Fehlern, denn das LogRepo kann diese nicht vorab filtern
        $logs = array_filter($logs->toArray(), function (\TYPO3\CMS\Belog\Domain\Model\LogEntry $log) {
            return $log->getError() == 2;
        });


        // Mails verschicken
        $this->sendMails($logs);

        // Wir speichern, wann der Task aufgerufen wurde, da wir leider selber nicht abfragen können, wann der eigene Task gelaufen ist
        $this->registry->set('datamints_error_report', 'lastExecutedTimestamp', time());

        return 0;
    }

    private function initializeDependencies (): void
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->registry = $this->objectManager->get(\TYPO3\CMS\Core\Registry::class);
        $this->mailService = GeneralUtility::makeInstance(\Datamints\DatamintsErrorReport\Services\MailService::class);
    }

    /**
     * Baut das Constraint-Objekt zusammen mit den Bedingungen
     *
     * @return \TYPO3\CMS\Belog\Domain\Model\Constraint
     */
    protected function getConstraint (): Constraint
    {

        /** @var Constraint $constraint */
        $constraint = GeneralUtility::makeInstance(Constraint::class);
        $constraint->setStartTimestamp(intval($this->registry->get('datamints_error_report', 'lastExecutedTimestamp')));
        //$constraint->setStartTimestamp(0); // Für Testzwecke alle Reports ausgeben (wird aber nochmal begrenzt, also keine Sorge)
        $constraint->setNumber(intval($this->input->getOption('max'))); // Maximale Anzahl
        $constraint->setEndTimestamp(time());
        return $constraint;
    }

    protected function sendMails ($logs)
    {
        $mailTemplate = $this->mailService->getRenderedReport('Error', ['logs' => $logs, 'name' => $this->input->getOption('name')]);


        $recipients = $this->input->getOption('recipient');

        foreach ($recipients as $recipient) {
            $this->mailService->sendMailByString($recipient, 'Error Report: ' . $this->input->getOption('name'), $mailTemplate);
        }

    }
}
