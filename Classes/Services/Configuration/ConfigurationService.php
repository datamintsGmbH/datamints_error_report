<?php

namespace Datamints\DatamintsErrorReport\Services\Configuration;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
    protected array $settings;
    protected array $moduleSettings;
    protected string $extensionName;

    public function __construct(
        ConfigurationManagerInterface $configurationManager,
        ExtensionConfiguration $extensionConfiguration
    ) {
        $this->configurationManager = $configurationManager;

        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * Very important to call this init-function before using any functino in this service.
     * This process is necessary to gain more dynamic that every extension can use this service
     */
    public function initSettings(string $extension): void
    {
        $this->extensionName = $extension;

        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
            GeneralUtility::underscoredToUpperCamelCase($this->extensionName)
        );
        if(isset($this->getSettings()['module.']['tx_' . str_replace('_', '', $this->extensionName) . '.'])) {
            $this->moduleSettings = GeneralUtility::removeDotsFromTS(
                $this->getSettings()['module.']['tx_' . str_replace('_', '', $this->extensionName) . '.']['settings.']
            );
        }
        if(isset($this->getSettings()['plugin.']['tx_' . str_replace('_', '', $this->extensionName) . '.'])) {
            $this->settings = GeneralUtility::removeDotsFromTS(
                $this->settings['plugin.']['tx_' . str_replace('_', '', $this->extensionName) . '.']['settings.']
            );
        }
    }

    /**
     * Fetches the typoscript setup
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Fetches the module settings. Use the parameter to change the extension context TS
     */
    public function getModuleSettings(): array
    {
        return $this->moduleSettings;
    }

    /**
     * @param string|null $pluginName
     * @return array
     */
    public function getFrameworkConfiguration($pluginName = null)
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            GeneralUtility::underscoredToUpperCamelCase($this->extensionName),
            $pluginName
        );
    }

    /**
     * @return ConfigurationManagerInterface
     */
    public function getConfigurationManager()
    {
        return $this->configurationManager;
    }

    /**
     * @return array
     */
    public function getExtensionConfiguration()
    {
        return $this->extensionConfiguration->get($this->extensionName);
    }
}
