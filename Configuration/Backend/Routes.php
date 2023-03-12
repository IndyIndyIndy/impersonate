<?php

/***
 *
 * This file is part of the "Impersonate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
 *
 ***/

/**
 * Definitions for routes provided by EXT:impersonate
 */
return [
    'impersonate_frontendlogin' => [
        'path' => '/impersonate/login',
        'target' => \ChristianEssl\Impersonate\Controller\FrontendLoginController::class . '::loginAction',
    ],
];
