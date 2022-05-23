<?php
namespace ChristianEssl\Impersonate\Middleware;

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

use ChristianEssl\Impersonate\Authentication\AuthService;
use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Logs in a frontend user without a password - use with care!
 */
class FrontendUserAuthenticator implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $impersonateData = $request->getQueryParams()['tx_impersonate'] ?? [];
        if (isset($impersonateData['timeout'], $impersonateData['user'], $impersonateData['verification'])
            && $impersonateData['timeout'] > time()
            && VerificationUtility::verifyImpersonateData($impersonateData)
        ) {
            ExtensionManagementUtility::addService(
                'impersonate',
                'auth',
                AuthService::class,
                [
                    'title' => 'Temporary AuthService for impersonating a user',
                    'description' => 'Temporary AuthService for impersonating a user',
                    'subtype' => 'authUserFE,getUserFE',
                    'available' => true,
                    'priority' => 100,
                    'quality' => 70,
                    'os' => '',
                    'exec' => '',
                    'className' => AuthService::class,
                ]
            );

            $GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth'] = [
                'setup' => [
                    'FE_alwaysFetchUser' => true
                ]
            ];
        }

        return $handler->handle($request);
    }
}
