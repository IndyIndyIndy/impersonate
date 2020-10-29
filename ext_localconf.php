<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpUndefinedVariableInspection */

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'ChristianEssl.Impersonate',
            'Login',
            [
                'FrontendLogin' => 'login'
            ],
            // non-cacheable actions
            [
                'FrontendLogin' => 'login'
            ]
        );

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['actions'][] =
            \ChristianEssl\Impersonate\Hooks\DatabaseRecordListHooks::class;
    }
);