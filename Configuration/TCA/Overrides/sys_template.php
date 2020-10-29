<?php
defined('TYPO3_MODE') or die();

/**
 * Static TypoScript
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'impersonate',
    'Configuration/TypoScript',
    'Impersonate'
);
