<?php

namespace ChristianEssl\Impersonate\Service;

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

use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

/**
 * Configuration utility
 */
class ConfigurationService
{
    public function __construct(
        protected readonly BackendConfigurationManager $configurationManager,
        protected readonly ConnectionPool $connectionPool,
        protected readonly TypoScriptService $typoScriptService
    ) {}

    /**
     * @return int
     * @throws Exception
     */
    public function getRedirectPageId(): int
    {
        $typoScriptSetup = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
            $this->configurationManager->getTypoScriptSetup($this->getRequest())
        );

        if (isset($typoScriptSetup['module']['tx_impersonate'])) {
            $configuration = $typoScriptSetup['module']['tx_impersonate'];

            if ($this->redirectPageIdExists($configuration)) {
                return (int)$configuration['settings']['loginRedirectPid'];
            }
        }

        return $this->getRootPageId();
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return bool
     */
    protected function redirectPageIdExists(array $configuration): bool
    {
        return isset($configuration['settings']['loginRedirectPid']) && $configuration['settings']['loginRedirectPid'] > 0;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getRootPageId(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');

        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

        $rootPage = $queryBuilder
            ->select('uid')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, Connection::PARAM_INT))
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAssociative();

        if (!is_array($rootPage)) {
            // Early validation of root page - it must always be given
            throw new \RuntimeException('No root page defined/ found', 1678628605);
        }

        return (int)$rootPage['uid'];
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
