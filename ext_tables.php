<?php /** @noinspection PhpUndefinedVariableInspection */
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('impersonate', 'Configuration/TypoScript', 'Impersonate');