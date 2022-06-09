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

namespace ChristianEssl\Impersonate\Utility;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

class ConfigurationUtility
{
    /**
     * @throws Exception
     */
    public static function getRedirectPageId(): int
    {
        $configurationManager = GeneralUtility::makeInstance(BackendConfigurationManager::class);
        $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);

        $typoScriptSetup = $typoScriptService->convertTypoScriptArrayToPlainArray(
            $configurationManager->getTypoScriptSetup()
        );

        if (isset($typoScriptSetup['module']['tx_impersonate'])) {
            $configuration = $typoScriptSetup['module']['tx_impersonate'];

            if (self::redirectPageIdExists($configuration)) {
                return (int)$configuration['settings']['loginRedirectPid'];
            }
        }

        return self::getRootPageId();
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return bool
     */
    protected static function redirectPageIdExists($configuration): bool
    {
        return isset($configuration['settings']['loginRedirectPid']) && $configuration['settings']['loginRedirectPid'] > 0;
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function getRootPageId(): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

        $rootPage = $queryBuilder
            ->select('uid')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('is_siteroot', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAssociative();

        if (empty($rootPage)) {
            // Early validation of root page - it must always be given
            throw new \RuntimeException('No root page defined/ found', 1678628605);
        }

        return (int)$rootPage['uid'];
    }
}
