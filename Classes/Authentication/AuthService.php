<?php

namespace ChristianEssl\Impersonate\Authentication;

use ChristianEssl\Impersonate\Utility\VerificationUtility;
use TYPO3\CMS\Core\Authentication\AuthenticationService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AuthService extends AuthenticationService
{
    public function getUser()
    {
        $uid = (int)GeneralUtility::_GET('tx_impersonate')['user'];
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('fe_users');
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
