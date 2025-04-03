<?php

namespace ChristianEssl\Impersonate\Controller;

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

use ChristianEssl\Impersonate\Exception\NoUserIdException;
use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\PreviewUriBuilder;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles logging in a frontend user with the given uid
 */
class FrontendLoginController
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return RedirectResponse
     * @throws NoUserIdException
     * @throws Exception
     */
    public function loginAction(ServerRequestInterface $request): ResponseInterface
    {
        $siteIdentifier = (string)$request->getQueryParams()['site'];
        $userUid = (int)$request->getQueryParams()['user'];

        if ($userUid > 0) {
            // redirect to site root should always be safe for login purposes -> "login redirect" happens in
            // RedirectHandler middleware
            $pageUid = GeneralUtility::makeInstance(SiteFinder::class)
                                     ->getSiteByIdentifier($siteIdentifier)
                                     ->getRootPageId();
            $additionalGetVars = [
                'tx_impersonate' => [
                    'site' => $siteIdentifier,
                    'timeout' => $timeout = time() + 60,
                    'user' => $userUid,
                    'verification' => VerificationUtility::buildVerificationHash(
                        $timeout,
                        $siteIdentifier,
                        $userUid
                    ),
                ],
            ];
            $previewUrl = (string)PreviewUriBuilder::create($pageUid)
                                                   ->withAdditionalQueryParameters($additionalGetVars)
                                                   ->buildUri();

            return new RedirectResponse($previewUrl);
        }

        throw new NoUserIdException('No user was given.');
    }
}
