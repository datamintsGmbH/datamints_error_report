<?php
defined('TYPO3_MODE') || die();

call_user_func(static function() {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('datamints_error_report', 'Configuration/TypoScript', 'datamints Error Report');

});
