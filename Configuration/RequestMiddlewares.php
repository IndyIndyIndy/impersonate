<?php

return [
    'frontend' => [
        'christianessl/impersonate/authentication' => [
            'target' => \ChristianEssl\Impersonate\Middleware\FrontendUserAuthenticator::class,
            'before' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
