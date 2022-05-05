<?php
namespace ChristianEssl\Impersonate\Hooks;

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

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList;
use TYPO3\CMS\Recordlist\RecordList\RecordListHookInterface;

/**
 * Hooks for DatabaseRecordList, implementing the icons for impersonating a frontend user
 */
class DatabaseRecordListHooks implements RecordListHookInterface
{
    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * @var bool
     */
    protected static $buttonJavasScriptLoaded = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * Modifies Web>List clip icons (copy, cut, paste, etc.) of a displayed row
     *
     * @param string $table The current database table
     * @param array $row The current record row
     * @param array $cells The default clip-icons to get modified
     * @param DatabaseRecordList $parentObject Instance of calling object
     *
     * @return array The modified clip-icons
     */
    public function makeClip($table, $row, $cells, &$parentObject)
    {
        return $cells;
    }

    /**
     * Modifies Web>List control icons of a displayed row
     *
     * @param string $table The current database table
     * @param array $row The current record row
     * @param array $cells The default control-icons to get modified
     * @param object $parentObject Instance of calling object
     *
     * @return array The modified control-icons
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function makeControl($table, $row, $cells, &$parentObject)
    {
        if ($this->isButtonPlacementAllowed($table)) {
            $this->addImpersonateButton($cells, $row);
        }
        return $cells;
    }

    /**
     * Modifies Web>List header row columns/cells
     *
     * @param string $table The current database table
     * @param array $currentIdList Array of the currently displayed uids of the table
     * @param array $headerColumns An array of rendered cells/columns
     * @param object $parentObject Instance of calling (parent) object
     *
     * @return array Array of modified cells/columns
     */
    public function renderListHeader($table, $currentIdList, $headerColumns, &$parentObject)
    {
        return $headerColumns;
    }

    /**
     * Modifies Web>List header row clipboard/action icons
     *
     * @param string $table The current database table
     * @param array $currentIdList Array of the currently displayed uids of the table
     * @param array $cells An array of the current clipboard/action icons
     * @param object $parentObject Instance of calling (parent) object
     *
     * @return array Array of modified clipboard/action icons
     */
    public function renderListHeaderActions($table, $currentIdList, $cells, &$parentObject)
    {
        return $cells;
    }

    /**
     * @param array $cells
     * @param array $userRow
     *
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function addImpersonateButton(&$cells, $userRow)
    {
        $userId = $userRow['uid'];
        $uri = $this->buildFrontendLoginUri($userId);

        $buttonText = $this->translate('button.impersonate');
        $iconMarkup = $this->iconFactory->getIcon('actions-system-backend-user-switch', Icon::SIZE_SMALL)->render();

        $button = '
            <a  class="btn btn-default t3-impersonate-button" 
                href="'.$uri.'" target="newTYPO3frontendWindow" 
                title="'.$buttonText.'">
	                '.$iconMarkup.'	
            </a>';

        $cells['impersonate'] = $button;
    }

    /**
     * @param $userId
     *
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function buildFrontendLoginUri($userId)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $uriBuilder = $objectManager->get(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute('impersonate_frontendlogin', ['uid' => $userId]);
    }

    /**
     * @param string $table
     *
     * @return bool
     */
    protected function isButtonPlacementAllowed($table)
    {
        return $table === 'fe_users' &&
            $GLOBALS['BE_USER'] instanceof BackendUserAuthentication &&
            $GLOBALS['BE_USER']->isAdmin();
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function translate($key)
    {
        return $GLOBALS['LANG']->sL('LLL:EXT:impersonate/Resources/Private/Language/locallang.xlf:'.$key);
    }
}
