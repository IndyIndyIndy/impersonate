<?php

namespace ChristianEssl\Impersonate\Utility;

/***
 *
 * This file is part of the "Impersonate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
 *      2022 Axel Böswetter <boeswetter@portrino.de>, https://www.portrino.de
 *
 ***/

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class VerificationUtility
{
    /**
     * @param int $timeout
     * @param string $siteIdentifier
     * @param int $user
     * @return string
     */
    public static function buildVerificationHash(int $timeout, string $siteIdentifier, int $user): string
    {
        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication && $GLOBALS['BE_USER']->isAdmin()) {
            return hash(
                'sha256',
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] .
                $GLOBALS['BE_USER']->getSession()->getIdentifier() .
                $timeout .
                $siteIdentifier .
                $user
            );
        }
        return '';
    }

    /**
     * @param array<string, int|string> $impersonateData
     * @return bool
     */
    public static function verifyImpersonateData(array $impersonateData): bool
    {
        if (
            isset($impersonateData['timeout'], $impersonateData['user'], $impersonateData['site'], $impersonateData['verification'])
            && $impersonateData['timeout'] > time()
            && $GLOBALS['BE_USER'] instanceof BackendUserAuthentication
            && $GLOBALS['BE_USER']->isAdmin()
        ) {
            return $impersonateData['verification'] === hash(
                'sha256',
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] .
                $GLOBALS['BE_USER']->getSession()->getIdentifier() .
                $impersonateData['timeout'] .
                $impersonateData['site'] .
                $impersonateData['user']
            );
        }
        return false;
    }
}
