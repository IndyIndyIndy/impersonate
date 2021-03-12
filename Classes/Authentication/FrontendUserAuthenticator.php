<?php
namespace ChristianEssl\Impersonate\Authentication;

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

use ChristianEssl\Impersonate\Exception\NoAdminUserException;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Error\Http\ServiceUnavailableException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Logs in a frontend user without a password - use with care!
 */
class FrontendUserAuthenticator
{

    /**
     * @param integer $uid
     * @throws ServiceUnavailableException
     * @throws NoAdminUserException
     */
    public function authenticate($uid)
    {
        if (!$this->isAdminUserLoggedIn()) {
            throw new NoAdminUserException('Missing backend administrator authentication.');
        }
        $this->loginFrontendUser((int)$uid);
    }

    /**
     * Login the frontend user
     *
     * @param int $uid
     */
    protected function loginFrontendUser(int $uid)
    {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            'impersonate',
            'auth',
            \ChristianEssl\Impersonate\Authentication\AuthService::class,
            [
                'title' => 'Temporary AuthService for impersonating a user',
                'description' => 'Temporary AuthService for impersonating a user',
                'subtype' => 'authUserFE,getUserFE',
                'available' => true,
                'priority' => 100,
                'quality' => 70,
                'os' => '',
                'exec' => '',
                'className' => \ChristianEssl\Impersonate\Authentication\AuthService::class,
            ]
        );

        $frontendUser = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
        $frontendUser->svConfig = [
            'setup' => [
                'FE_alwaysFetchUser' => true
            ]
        ];

        $frontendUser->start();
        $frontendUser->unpack_uc();
        $frontendUser->storeSessionData();
    }

    /**
     * @return bool
     */
    protected function isAdminUserLoggedIn()
    {
        return $GLOBALS['BE_USER'] instanceof BackendUserAuthentication &&
            $GLOBALS['BE_USER']->isAdmin();
    }
}
