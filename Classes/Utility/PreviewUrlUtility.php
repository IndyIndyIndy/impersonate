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

/**
 * Get the preview url
 */
class PreviewUrlUtility
{
    public static function getPreviewUrl($pageId)
    {
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
    }

}