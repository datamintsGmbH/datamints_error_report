<?php
/**
 * Copyright (c) 2017. Mark Weisgerber - datamints GmbH (m.weisgerber@datamints.com)
 */

namespace Datamints\DatamintsErrorReport\Services;

use Datamints\DatamintsErrorReport\Services\Configuration\ConfigurationService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

abstract class AbstractService implements SingletonInterface
{
    /**
     * configurationService
     *
     * @var \Datamints\DatamintsErrorReport\Services\Configuration\ConfigurationService
     */
    protected \Datamints\DatamintsErrorReport\Services\Configuration\ConfigurationService $configurationService;

    public function __construct ()
    {
        $this->configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
    }

    /**
     * Liefert das TS der Extension
     *
     * @return array
     */
    public function getSettings (): array
    {
        return $this->configurationService->getSettings();
    }

    /**
     * Liefert die ExtensionConfig
     *
     * @return array
     */
    public function getExtensionConfig (): array
    {
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][\Datamints\DatamintsErrorReport\Utility\ErrorReportUtility::EXTENSION_NAME]);
    }

    /**
     * @param \Datamints\DatamintsErrorReport\Services\Configuration\ConfigurationService $configurationService
     */
    public function injectConfigurationService(\Datamints\DatamintsErrorReport\Services\Configuration\ConfigurationService $configurationService): void
    {
        $this->configurationService = $configurationService;
    }
}
