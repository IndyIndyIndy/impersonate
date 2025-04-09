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

namespace ChristianEssl\Impersonate\Service;

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
                return $site->getRouter()
                            ->generateUri((int)$siteSettings['tx_impersonate']['loginRedirectPid'])
                            ->__toString();
            }
            if (isset($siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid'])
                && (int)$siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid'] > 0
            ) {
                // @deprecated: Will be removed in v14 compatible version
                trigger_error(
                    'Please use new configuration path "tx_impersonate.loginRedirectPid" instead of "module.tx_impersonate.settings.loginRedirectPid".',
                    E_USER_DEPRECATED
                );
                return $site->getRouter()
                            ->generateUri((int)$siteSettings['module']['tx_impersonate']['settings']['loginRedirectPid'])
                            ->__toString();
            }
        } catch (\Exception $e) {
            return '';
        }
        return '';
    }
}
