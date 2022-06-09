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

namespace ChristianEssl\Impersonate\Controller;

use ChristianEssl\Impersonate\Exception\NoUserIdException;
use ChristianEssl\Impersonate\Utility\ConfigurationUtility;
use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\PreviewUriBuilder;
use TYPO3\CMS\Core\Http\RedirectResponse;

/**
 * Handles logging in a frontend user with the given uid
 */
class FrontendLoginController
{
    /**
     * @throws NoUserIdException
     * @throws Exception
     */
    public function loginAction(ServerRequestInterface $request): ResponseInterface
    {
        $uid = (int)$request->getQueryParams()['uid'];

        if (empty($uid)) {
            throw new NoUserIdException('No user was given.');
        }

        $pageUid = ConfigurationUtility::getRedirectPageId();
        $additionalGetVars = [
            'tx_impersonate' => [
                'timeout' => $timeout = time() + 60,
                'user' => $user = (int)$request->getQueryParams()['uid'],
                'verification' => VerificationUtility::buildVerificationHash($timeout, $user),
            ],
        ];
        $previewUrl = (string)PreviewUriBuilder::create($pageUid)
            ->withAdditionalQueryParameters($additionalGetVars)
            ->buildUri();

        return new RedirectResponse($previewUrl);
    }
}
