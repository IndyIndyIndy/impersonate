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

namespace ChristianEssl\Impersonate\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class VerificationUtility
{
    public static function buildVerificationHash(int $timeout, int $user): string
    {
        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication && $GLOBALS['BE_USER']->isAdmin()) {
            return md5(
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] .
                $GLOBALS['BE_USER']->getSession()->getIdentifier() .
                $timeout .
                $user
            );
        }
        return '';
    }

    public static function verifyImpersonateData(array $impersonateData): bool
    {
        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication && $GLOBALS['BE_USER']->isAdmin()) {
            return $impersonateData['verification'] === md5(
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] .
                    $GLOBALS['BE_USER']->getSession()->getIdentifier() .
                    $impersonateData['timeout'] .
                    $impersonateData['user']
            );
        }
        return false;
    }
}
