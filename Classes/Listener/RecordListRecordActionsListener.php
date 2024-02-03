<?php

namespace ChristianEssl\Impersonate\Listener;

/***
 *
 * This file is part of the "Impersonate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
 *      2022 Axel Böswetter <boeswetter@portrino.de>, https://www.portrino.de
 *
 ***/

use TYPO3\CMS\Backend\RecordList\Event\ModifyRecordListRecordActionsEvent;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Event listener for DatabaseRecordList, implementing the icons for impersonating a frontend user
 */
class RecordListRecordActionsListener
{
    /**
     * @param ModifyRecordListRecordActionsEvent $event
     * @throws RouteNotFoundException
     */
    public function __invoke(ModifyRecordListRecordActionsEvent $event): void
    {
        if ($event->getTable() === 'fe_users'
            && $GLOBALS['BE_USER'] instanceof BackendUserAuthentication
            && $GLOBALS['BE_USER']->isAdmin()
        ) {
            $event->setAction(
                $this->addImpersonateButton($event->getRecord()),
                'impersonate',
                'primary',
                '',
                'delete'
            );
        }
    }

    /**
     * @param array<string, mixed> $userRow
     * @return string
     * @throws RouteNotFoundException
     */
    protected function addImpersonateButton(array $userRow): string
    {
        $userId = $userRow['uid'];
        $uri = $this->buildFrontendLoginUri($userId);

        $buttonText = $this->translate('button.impersonate');
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $iconMarkup = $iconFactory->getIcon('actions-system-backend-user-switch', Icon::SIZE_SMALL)->render();

        return '
            <a class="btn btn-default t3-impersonate-button"
               href="' . $uri . '" target="newTYPO3frontendWindow"
               title="' . $buttonText . '">
	                ' . $iconMarkup . '
            </a>';
    }

    /**
     * @param int $userId
     * @return string
     * @throws RouteNotFoundException
     */
    protected function buildFrontendLoginUri(int $userId): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute('impersonate_frontendlogin', ['uid' => $userId]);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function translate(string $key): string
    {
        return $GLOBALS['LANG']->sL('LLL:EXT:impersonate/Resources/Private/Language/locallang.xlf:' . $key);
    }
}
