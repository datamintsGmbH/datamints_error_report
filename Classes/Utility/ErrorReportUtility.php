<?php

namespace Datamints\DatamintsErrorReport\Utility;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ErrorReportUtility
{
    public const EXTENSION_NAME = "datamints_error_report";

    /**
     * @param string $templatePath
     * @param string $controller
     *
     * @return StandaloneView
     */
    public static function getFluidStandaloneViewWithTemplate ($templatePath = '', $controller = '')
    {
        /** @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        // @TODO fetch paths from typoscript
        $standaloneView->setLayoutRootPaths(['EXT:datamints_error_report/Resources/Private/Layouts']);
        $standaloneView->setPartialRootPaths(['EXT:datamints_error_report/Resources/Private/Partials']);
        $standaloneView->setTemplateRootPaths(['EXT:datamints_error_report/Resources/Private/Templates']);
        $standaloneView->setTemplate($templatePath);

        if ($controller) {
            $standaloneView->getRenderingContext()->setControllerName($controller);
        }

        return $standaloneView;
    }
}
