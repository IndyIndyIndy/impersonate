<?php

/*
 * This file is part of the "Impersonate" Extension for TYPO3 CMS.
 *
 * (c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
 *     2022 Axel Böswetter <boeswetter@portrino.de>, https://www.portrino.de
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace ChristianEssl\Impersonate\Controller;

use ChristianEssl\Impersonate\Exception\NoUserIdException;
use ChristianEssl\Impersonate\Utility\ConfigurationUtility;
use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Routing\UnableToLinkToPageException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles logging in a frontend user with the given uid
 */
class FrontendLoginController
{
    /**
     * @throws NoUserIdException
     * @throws UnableToLinkToPageException
     */
    public function loginAction(ServerRequestInterface $request): ResponseInterface
    {
        $uid = (int)$request->getQueryParams()['uid'];

        if (empty($uid)) {
            throw new NoUserIdException('No user was given.');
        }

        $additionalGetVars = GeneralUtility::implodeArrayForUrl('tx_impersonate', [
            'timeout' => $timeout = time() + 60,
            'user' => $user = (int)$request->getQueryParams()['uid'],
            'verification' => VerificationUtility::buildVerificationHash($timeout, $user)
        ]);
        $pageUid = ConfigurationUtility::getRedirectPageId();
        $previewUrl = BackendUtility::getPreviewUrl(
            $pageUid,
            '',
            null,
            '',
            '',
            $additionalGetVars
        );

        return new RedirectResponse($previewUrl);
    }
}
