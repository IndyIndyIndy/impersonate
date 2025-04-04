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

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

/**
 * Configuration utility
 */
class ConfigurationService
{
    public function __construct(
        protected readonly BackendConfigurationManager $configurationManager,
        protected readonly SiteFinder $siteFinder,
        protected readonly TypoScriptService $typoScriptService
    ) {}

    /**
     * @param string $siteIdentifier
     * @return string
     */
    public function getRedirectPageUri(string $siteIdentifier): string
    {
        try {
            $site = $this->siteFinder->getSiteByIdentifier($siteIdentifier);
            $siteSettings = $site->getSettings()->getAll();
            $typoScriptSetup = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
                $this->configurationManager->getTypoScriptSetup()
            );

            if (isset($typoScriptSetup['module']['tx_impersonate']['settings']['loginRedirectPid'])
                && (int)$typoScriptSetup['module']['tx_impersonate']['settings']['loginRedirectPid'] > 0
            ) {
                return $site->getRouter()->generateUri((int)$typoScriptSetup['module']['tx_impersonate']['settings']['loginRedirectPid']);
            }
            if (isset($siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid'])
                && (int)$siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid'] > 0
            ) {
                return $site->getRouter()->generateUri((int)$siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid']);
            }
        } catch (\Exception $e) {
            return '';
        }
        return '';
    }
}
