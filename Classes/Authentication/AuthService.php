<?php

namespace ChristianEssl\Impersonate\Authentication;

use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\AuthenticationService;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AuthService extends AuthenticationService
{
    /**
     * @return array<string, mixed>|false User array or FALSE
     * @throws Exception
     */
    public function getUser(): array|bool
    {
        $uid = (int)$this->getRequest()->getQueryParams()['tx_impersonate']['user'];
        $queryBuilder = (GeneralUtility::makeInstance(ConnectionPool::class))
                                       ->getQueryBuilderForTable('fe_users');
        $queryBuilder
            ->select('*')
            ->from('fe_users')
            ->where($queryBuilder->expr()->eq(
                'uid',
                $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
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
        if (VerificationUtility::verifyImpersonateData($this->getRequest()->getQueryParams()['tx_impersonate'])) {
            $result = 200;
        }
        return $result;
    }

    private function getRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }
}
