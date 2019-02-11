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

use ChristianEssl\Impersonate\Utility\ConfigurationUtility;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Logs in a frontend user without a password - use with care!
 */
class FrontendUserAuthenticator
{

    /**
     * @param integer $uid
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     */
    public function authenticate($uid)
    {
        $this->buildTSFE();
        $this->loginFrontendUser($uid);
        $this->loginFrontendUser($uid);
    }

    /**
     * @todo: fix this for TYPO3 10
     * Initializing the TypoScriptFrontendController this way is deprecated, but the new
     * TypoScriptFrontendInitialization middleware is not production ready yet - fix this in TYPO3 10
     *
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     */
    protected function buildTSFE()
    {
        $rootPageId = ConfigurationUtility::getRootPageId();
        $GLOBALS['TSFE'] = new TypoScriptFrontendController(null, $rootPageId, 0);
        $GLOBALS['TSFE']->setLogger(new NullLogger());
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
    }

    /**
     * Login the frontend user
     *
     * @param integer $uid
     */
    protected function loginFrontendUser($uid)
    {
        $GLOBALS['TSFE']->fe_user->is_permanent = false;
        $GLOBALS['TSFE']->fe_user->checkPid = false;
        $GLOBALS['TSFE']->fe_user->createUserSession(['uid' => $uid]);
        $GLOBALS['TSFE']->fe_user->user = $GLOBALS['TSFE']->fe_user->fetchUserSession();
        $GLOBALS['TSFE']->fe_user->fetchGroupData();
        $GLOBALS['TSFE']->loginUser = true;
        $GLOBALS['TSFE']->fe_user->forceSetCookie = false;
        $GLOBALS['TSFE']->fe_user->setAndSaveSessionData('Authenticated via impersonate extension', true);
        $this->setSessionCookie($GLOBALS['TSFE']->fe_user);
    }

    /**
     * Set the session cookie after login (otherwise the login will fail on first time, if no session cookie exists yet)
     *
     * @param FrontendUserAuthentication $user
     */
    protected function setSessionCookie(FrontendUserAuthentication $user)
    {
        $cookieDomain = $this->getCookieDomain($user);
        $cookiePath = $cookieDomain ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
        $cookieSecure = (bool)$GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieSecure'] && GeneralUtility::getIndpEnv('TYPO3_SSL');
        setcookie($user->name, $user->id, 0, $cookiePath, $cookieDomain, $cookieSecure, true);
    }

    /**
     * Gets the domain to be used on setting cookies.
     * Code taken from typo3/sysext/core/Classes/Authentication/AbstractUserAuthentication
     *
     * @param FrontendUserAuthentication $user
     *
     * @return string The domain to be used on setting cookies
     */
    protected function getCookieDomain(FrontendUserAuthentication $user)
    {
        $result = '';
        $cookieDomain = $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'];
        // If a specific cookie domain is defined for a given TYPO3_MODE,
        // use that domain
        if (!empty($GLOBALS['TYPO3_CONF_VARS'][$user->loginType]['cookieDomain'])) {
            $cookieDomain = $GLOBALS['TYPO3_CONF_VARS'][$user->loginType]['cookieDomain'];
        }
        if ($cookieDomain) {
            if ($cookieDomain[0] === '/') {
                $match = [];
                $matchCnt = @preg_match($cookieDomain, GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'), $match);
                if ($matchCnt) {
                    $result = $match[0];
                }
            } else {
                $result = $cookieDomain;
            }
        }
        return $result;
    }
}