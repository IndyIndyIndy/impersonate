<?php

namespace ChristianEssl\Impersonate\Controller;

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

use ChristianEssl\Impersonate\Exception\NoUserIdException;
use ChristianEssl\Impersonate\Service\ConfigurationService;
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
    public function __construct(protected readonly ConfigurationService $configurationService) {}

    /**
     * @param ServerRequestInterface $request
     *
     * @return RedirectResponse
     * @throws NoUserIdException
     * @throws Exception
     */
    public function loginAction(ServerRequestInterface $request): ResponseInterface
    {
        $userUid = (int)$request->getQueryParams()['uid'];

        if ($userUid > 0) {
            $pageUid = $this->configurationService->getRedirectPageId();
            $additionalGetVars = [
                'tx_impersonate' => [
                    'timeout' => $timeout = time() + 60,
                    'user' => $userUid,
                    'verification' => VerificationUtility::buildVerificationHash(
                        $timeout,
                        $userUid
                    ),
                ],
            ];
            $previewUrl = (string)PreviewUriBuilder::create($pageUid)
                                                   ->withAdditionalQueryParameters($additionalGetVars)
                                                   ->buildUri();

            return new RedirectResponse($previewUrl);
        }

        throw new NoUserIdException('No user was given.', 1738245688);
    }
}
