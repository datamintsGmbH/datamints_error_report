<?php

namespace Datamints\DatamintsErrorReport\Services\Configuration;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ConfigurationService implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Core\Configuration\ExtensionConfiguration
     */
    protected $extensionConfiguration;

    public function __construct (ConfigurationManagerInterface $configurationManager, ExtensionConfiguration $extensionConfiguration)
    {
        $this->configurationManager = $configurationManager;
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * @param string|null $pluginName
     *
     * @return array
     */
    public function getSettings ($pluginName = null)
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'DatamintsErrorReport',
            $pluginName
        );
    }

    /**
     * @param string|null $pluginName
     *
     * @return array
     */
    public function getFrameworkConfiguration ($pluginName = null)
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'DatamintsErrorReport',
            $pluginName
        );
    }

    /**
     * @return ConfigurationManagerInterface
     */
    public function getConfigurationManager ()
    {
        return $this->configurationManager;
    }

    /**
     * @return array
     */
    public function getExtensionConfiguration ()
    {
        return $this->extensionConfiguration->get(\Datamints\DatamintsErrorReport\Utility\ErrorReportUtility::EXTENSION_NAME);
    }
}
