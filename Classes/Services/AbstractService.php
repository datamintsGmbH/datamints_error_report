<?php
/**
 * Copyright (c) 2017. Mark Weisgerber - datamints GmbH (m.weisgerber@datamints.com)
 */

namespace Datamints\DatamintsErrorReport\Services;

use Datamints\DatamintsErrorReport\Services\Configuration\ConfigurationService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

abstract class AbstractService implements SingletonInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Datamints\DatamintsErrorReport\Services\Configuration\ConfigurationService
     */
    protected $configurationService;

    public function __construct ()
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $this->configurationService = $this->objectManager->get(ConfigurationService::class);
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
        return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['datamints_error_report']);
    }

    /**
     * @return ConfigurationManagerInterface
     */
    public function getConfigurationManager (): ConfigurationManagerInterface
    {
        return $this->objectManager->get(ConfigurationManagerInterface::class);
    }

}
