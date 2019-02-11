<?php
namespace ChristianEssl\Impersonate\Utility;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Get the preview url
 */
class PreviewUrlUtility
{
    public static function getPreviewUrl($pageId)
    {
        // the cool way
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9000000) {
            $switchFocus = true;
            return BackendUtility::getPreviewUrl(
                $pageId,
                '',
                null,
                '',
                '',
                '',
                $switchFocus
            );
        } else {
            // the old way
            $protocolAndHost = self::getProtocolAndHost($pageId);
            return $protocolAndHost . '/index.php?id=' . $pageId;
        }
    }

    /**
     * The old preview url method from ViewModuleController in TYPO3 8.7
     *
     * @param integer $pageId
     *
     * @return string
     */
    protected static function getProtocolAndHost($pageId)
    {
        $protocolAndHost = '..';
        $domainName = self::getDomainName($pageId);
        if ($domainName) {
            // TCEMAIN.previewDomain can contain the protocol, check prevents double protocol URLs
            if (strpos($domainName, '://') !== false) {
                $protocolAndHost = $domainName;
            } else {
                $protocol = GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https' : 'http';
                $protocolAndHost = $protocol . '://' . $domainName;
            }
        }
        return $protocolAndHost;
    }

    /**
     * Get domain name for requested page id
     * Taken from ViewModuleController in TYPO3 8.7
     *
     * @param int $pageId
     * @return string|null Domain name from first sys_domains-Record or from TCEMAIN.previewDomain, NULL if neither is configured
     */
    protected static function getDomainName($pageId)
    {
        $previewDomainConfig = $GLOBALS['BE_USER']
            ->getTSConfig('TCEMAIN.previewDomain', BackendUtility::getPagesTSconfig($pageId));
        if ($previewDomainConfig['value']) {
            $domain = $previewDomainConfig['value'];
        } else {
            $domain = BackendUtility::firstDomainRecord(BackendUtility::BEgetRootLine($pageId));
        }
        return $domain;
    }

}