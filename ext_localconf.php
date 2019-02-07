<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpUndefinedVariableInspection */

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($extKey) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['actions'][] =
            \ChristianEssl\Impersonate\Hooks\DatabaseRecordListHooks::class;
    },
    $_EXTKEY
);