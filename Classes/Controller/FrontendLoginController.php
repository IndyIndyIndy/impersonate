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
use ChristianEssl\Impersonate\Exception\NoAdminUserException;
use ChristianEssl\Impersonate\Exception\NoUserIdException;
use ChristianEssl\Impersonate\Utility\PreviewUrlUtility;
use TYPO3\CMS\Core\Error\Http\ServiceUnavailableException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Handles logging in a frontend user with the given uid
 */
class FrontendLoginController extends ActionController
{
    /**
     * @param FrontendUser $user
     *
     * @throws NoAdminUserException
     * @throws NoUserIdException
     * @throws ServiceUnavailableException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function loginAction(FrontendUser $user)
    {
        if ($user) {
            $this->authenticateFrontendUser($user);

            if (
                isset($this->settings['loginRedirectPid']) &&
                $this->settings['loginRedirectPid'] > 0
            ) {
                $previewUrl = PreviewUrlUtility::getPreviewUrl((int)$this->settings['loginRedirectPid']);
                $this->redirectToUri($previewUrl);
            }

            $this->redirectToUri('/');
        }

        throw new NoUserIdException('No user was given.');
    }

    /**
     * @param FrontendUser $user
     *
     * @throws ServiceUnavailableException
     * @throws NoAdminUserException
     */
    protected function authenticateFrontendUser($user)
    {
        $frontendUserAuthenticator = GeneralUtility::makeInstance(FrontendUserAuthenticator::class);
        $frontendUserAuthenticator->authenticate($user->getUid());
    }

}