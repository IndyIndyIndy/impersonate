<?php

return [
    'frontend' => [
        'christianessl/impersonate/authentication' => [
            'target' => \ChristianEssl\Impersonate\Middleware\FrontendUserAuthenticator::class,
            'before' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
        'christianessl/impersonate/redirecthandler' => [
            'target' => \ChristianEssl\Impersonate\Middleware\RedirectHandler::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
            'before' => [
                'typo3/cms-redirects/redirecthandler',
            ],
        ],
    ],
];
