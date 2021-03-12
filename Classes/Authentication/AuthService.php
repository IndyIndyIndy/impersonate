<?php

declare(strict_types=1);

namespace ChristianEssl\Impersonate\Authentication;

use TYPO3\CMS\Core\Authentication\AuthenticationService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AuthService extends AuthenticationService
{
    public function getUser()
    {
        $uid = (int)GeneralUtility::_GET('uid');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');
        $queryBuilder
            ->select('*')
            ->from('fe_users')
            ->where($queryBuilder->expr()->eq(
                'uid',
                $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
            ));

        return $queryBuilder->execute()->fetch();
    }

    public function authUser(array $user): int
    {
        return (int) (GeneralUtility::_GET('route') === '/impersonate/login' &&
            $GLOBALS['BE_USER'] instanceof BackendUserAuthentication &&
            $GLOBALS['BE_USER']->isAdmin());
    }

}
