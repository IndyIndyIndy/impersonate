<?php

defined('TYPO3') || die();

(function () {
    $extensionKey = 'impersonate';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'Impersonate'
    );
})();
