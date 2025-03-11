<?php

namespace Datamints\DatamintsErrorReport\Command\Report;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2022 Mark Weisgerber <m.weisgerber@datamints.com>, datamints GmbH
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

/**
 *
 * @package datamints_error_report
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
    protected function configure ()
    {
        $this->setDescription('Sends a bundled bug report of the bugs collected since the last run');

        $this->addOption(
            'max',
            null,
            InputOption::VALUE_REQUIRED,
            'Maximum number of error messages sent simultaneously per email (protection against emails that are too large)',
            '50'
        );

        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'Name of the current system (to identify multiple TYPO3 instances)',
            'Draft'
        );

        $this->addOption(
            'recipient',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Recipients e-mail address. Multiple can be defined by specifying --recipient multiple times.',
            ['']
        );
    }

    /**
     * Sends a bundled bug report of the bugs collected since the last run
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


        // Fetch logs
        $logRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Belog\Domain\Repository\LogEntryRepository::class);
        $logs = $logRepository->findByConstraint($this->getConstraint());

        // If no logs were found, we don't need to continue
        if (count($logs) == 0) {
            return 0;
        }
        // Filter for errors, because the LogRepo cannot filter them in advance
        $logs = array_filter($logs->toArray(), function (\TYPO3\CMS\Belog\Domain\Model\LogEntry $log) {
            return $log->getError() == 2;
        });
        // Check if there are NO logs left after filtering, because in that case we will also stop!
        if (count($logs) == 0) {
            return 0;
        }

        // Send mails
        $this->sendMails($logs);

        // We save when the task was called because unfortunately we cannot query when our own task ran
        $this->registry->set(\Datamints\DatamintsErrorReport\Utility\ErrorReportUtility::EXTENSION_NAME, 'lastExecutedTimestamp', time());

        return 0;
    }

    /**
     * Initializes the dependencies
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    private function initializeDependencies (): void
    {
        $this->registry = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Registry::class);
        $this->mailService = GeneralUtility::makeInstance(\Datamints\DatamintsErrorReport\Services\MailService::class);
    }

    /**
     * Builds the constraint object along with the conditions
     *
     * @return \TYPO3\CMS\Belog\Domain\Model\Constraint
     */
    protected function getConstraint (): Constraint
    {
        /** @var Constraint $constraint */
        $constraint = GeneralUtility::makeInstance(Constraint::class);
        $constraint->setStartTimestamp(intval($this->registry->get(\Datamints\DatamintsErrorReport\Utility\ErrorReportUtility::EXTENSION_NAME, 'lastExecutedTimestamp')));
        //$constraint->setStartTimestamp(0); // Output all reports for test purposes (but will be limited again, so don't worry)
        $constraint->setNumber(intval($this->input->getOption('max'))); // Maximum amount of log entries
        $constraint->setEndTimestamp(time());
        return $constraint;
    }

    /**
     * Sends the emails
     *
     * @param $logs
     *
     * @return void
     */
    protected function sendMails ($logs)
    {
        $mailTemplate = $this->mailService->getRenderedReport('Error', ['logs' => $logs, 'name' => $this->input->getOption('name')]);

        $recipients = $this->input->getOption('recipient');

        foreach ($recipients as $recipient) {
            $this->mailService->sendMailByString($recipient, 'Error Report: ' . $this->input->getOption('name'), $mailTemplate);
        }

    }
}
