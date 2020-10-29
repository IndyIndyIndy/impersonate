<?php /** @noinspection PhpUndefinedVariableInspection */

/***************************************************************
 * Extension Manager/Repository config file for ext: "impersonate"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Impersonate',
    'description' => 'Impersonate frontend users from inside the TYPO3 Backend.',
    'category' => 'misc',
    'author' => 'Christian Eßl',
    'author_email' => 'indy.essl@gmail.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
