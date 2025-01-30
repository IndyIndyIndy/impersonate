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
     * @param int $user
     * @return string
     */
    public static function buildVerificationHash(int $timeout, int $user): string
    {
        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication && $GLOBALS['BE_USER']->isAdmin()) {
            return hash(
                'sha256',
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] .
                $GLOBALS['BE_USER']->getSession()->getIdentifier() .
                $timeout .
                $user
            );
        }
        return '';
    }

    /**
     * @param array<string, mixed> $impersonateData
     * @return bool
     */
    public static function verifyImpersonateData(array $impersonateData): bool
    {
        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication && $GLOBALS['BE_USER']->isAdmin()) {
            return $impersonateData['verification'] === hash(
                'sha256',
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] .
                $GLOBALS['BE_USER']->getSession()->getIdentifier() .
                $impersonateData['timeout'] .
                $impersonateData['user']
            );
        }
        return false;
    }
}
