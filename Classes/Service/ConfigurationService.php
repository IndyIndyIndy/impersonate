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

/**
 * Configuration utility
 */
class ConfigurationService
{
    public function __construct(
        protected readonly SiteFinder $siteFinder
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

            if (isset($siteSettings['tx_impersonate']['loginRedirectPid'])
                && (int)$siteSettings['tx_impersonate']['loginRedirectPid'] > 0
            ) {
                return $site->getRouter()->generateUri((int)$siteSettings['tx_impersonate']['loginRedirectPid']);
            }
            if (isset($siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid'])
                && (int)$siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid'] > 0
            ) {
                // @deprecated: Will be removed in v14 compatible version
                trigger_error(
                    'Please use new configuration path "tx_impersonate.loginRedirectPid" instead of "module.tx_impersonate.settings.loginRedirectPid".',
                    E_USER_DEPRECATED
                );
                return $site->getRouter()->generateUri((int)$siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid']);
            }
        } catch (\Exception $e) {
            return '';
        }
        return '';
    }
}
