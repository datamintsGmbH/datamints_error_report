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
     * Configure the command by defining the name, options and arguments
     */
    protected function configure (): void
    {
        $this->setDescription('Verschickt einen gebündelten Fehlerbericht der seit dem letzten Aufruf gesammelten Fehlern');

        $this->addOption(
            'from',
            'f',
            InputOption::VALUE_REQUIRED,
            'Datum oder Date-String, ab dem Daten für den Report gesammelt werden sollen (https://www.php.net/manual/en/datetime.formats.relative.php)',
            '-20 minutes'
        );

        $this->addOption(
            'recipient',
            'r',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'E-Mail Adresse des Empfängers. Mehrere können definiert werden, indem --recipient mehrmals spezifiert wird.',
            ['m.weisgerber@datamints.com']
        );
    }
}
