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

use ChristianEssl\Impersonate\Authentication\FrontendUserAuthenticator;
use ChristianEssl\Impersonate\Exception\NoUserIdException;
use ChristianEssl\Impersonate\Utility\ConfigurationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Error\Http\ServiceUnavailableException;
use TYPO3\CMS\Core\Error\Http\UnauthorizedException;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles logging in a frontend user with the given uid
 */
class FrontendLoginController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return RedirectResponse
     * @throws NoUserIdException
     * @throws ServiceUnavailableException
     * @throws UnauthorizedException
     */
    public function loginAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $uid = (int) $request->getQueryParams()['uid'];

        if (!empty($uid)) {
            $this->authenticateFrontendUser($uid);
            $pageId = ConfigurationUtility::getRedirectPageId();
            $previewUrl = $this->getPreviewUrl($pageId);
            return new RedirectResponse($previewUrl);
        }

        throw new NoUserIdException();
    }

    /**
     * @param integer $uid
     *
     * @throws ServiceUnavailableException
     * @throws UnauthorizedException
     */
    protected function authenticateFrontendUser($uid)
    {
        $frontendUserAuthenticator = GeneralUtility::makeInstance(FrontendUserAuthenticator::class);
        $frontendUserAuthenticator->authenticate($uid);
    }

    /**
     * @param integer $pageId
     *
     * @return string
     */
    protected function getPreviewUrl($pageId)
    {
        $switchFocus = true;
        return BackendUtility::getPreviewUrl(
            $pageId,
            '',
            null,
            '',
            '',
            '',
            $switchFocus
        );
    }

}