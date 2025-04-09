<?php

/**
 * Definitions for routes provided by EXT:impersonate
 */
return [
    'impersonate_frontendlogin' => [
        'path' => '/impersonate/login',
        'target' => \ChristianEssl\Impersonate\Controller\FrontendLoginController::class . '::loginAction',
    ],
];
