<?php

declare(strict_types=1);

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

namespace ChristianEssl\Impersonate\Authentication;

use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Authentication\AuthenticationService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AuthService extends AuthenticationService
{
    /**
     * @return array<string, mixed>|false User array or FALSE
     * @throws Exception
     */
    public function getUser(): array|bool
    {
        $uid = (int)GeneralUtility::_GET('tx_impersonate')['user'];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');
        $queryBuilder
            ->select('*')
            ->from('fe_users')
            ->where($queryBuilder->expr()->eq(
                'uid',
                $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
            ));

        return $queryBuilder->executeQuery()->fetchAssociative();
    }

    /**
     * Authenticate a user
     *
     * Returns one of the following status codes:
     *  >= 200: User authenticated successfully. No more checking is needed by other auth services.
     *  >= 100: User not authenticated; this service is not responsible. Other auth services will be asked.
     *  > 0:    User authenticated successfully. Other auth services will still be asked.
     *  <= 0:   Authentication failed, no more checking needed by other auth services.
     *
     * @param array<string, mixed> $user
     * @return int
     */
    public function authUser(array $user): int
    {
        $result = 100;
        if (VerificationUtility::verifyImpersonateData(GeneralUtility::_GET('tx_impersonate'))) {
            $result = 200;
        }
        return $result;
    }
}
