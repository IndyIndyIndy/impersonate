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
     * @param ServerRequestInterface $request
     *
     * @return RedirectResponse
     * @throws NoUserIdException
     * @throws UnableToLinkToPageException
     */
    public function loginAction(ServerRequestInterface $request): ResponseInterface
    {
        $uid = (int)$request->getQueryParams()['uid'];

        if (!empty($uid)) {
            $additionalGetVars = GeneralUtility::implodeArrayForUrl('tx_impersonate', [
                'timeout' => $timeout = time() + 60,
                'user' => $user = (int)$request->getQueryParams()['uid'],
                'verification' => VerificationUtility::buildVerificationHash($timeout, $user),
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

        throw new NoUserIdException('No user was given.');
    }
}
