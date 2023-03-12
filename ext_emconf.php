<?php

/** @noinspection PhpUndefinedVariableInspection */

/***************************************************************
 * Extension Manager/Repository config file for ext: "impersonate"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Impersonate',
    'description' => 'Impersonate frontend users from inside the TYPO3 Backend.',
    'category' => 'misc',
    'author' => 'Christian Eßl, Axel Böswetter',
    'author_email' => 'indy.essl@gmail.com, boeswetter@portrino.de',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
