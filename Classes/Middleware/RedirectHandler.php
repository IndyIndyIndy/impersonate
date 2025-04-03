<?php

namespace ChristianEssl\Impersonate\Middleware;

use ChristianEssl\Impersonate\Utility\ConfigurationUtility;
use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class RedirectHandler implements MiddlewareInterface
{
    public function __construct(
        protected readonly Context $context,
        protected readonly UriBuilder $uriBuilder
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var FrontendUserAuthentication $frontendUser */
        $frontendUser = $request->getAttribute('frontend.user');

        // Early return if
        // - no frontend user is available or frontend user is simulated via BE preview
        if ($frontendUser->getUserId() === null || $frontendUser->getUserId() === PHP_INT_MAX) {
            return $handler->handle($request);
        }

        $impersonateData = $request->getQueryParams()['tx_impersonate'] ?? [];
        if (isset($impersonateData['timeout'], $impersonateData['user'], $impersonateData['site'], $impersonateData['verification'])
            && $impersonateData['timeout'] > time()
            && VerificationUtility::verifyImpersonateData($impersonateData)
        ) {
            $siteIdentifier = (string)$impersonateData['site'];
            $redirectPageId = ConfigurationUtility::getRedirectPageId($siteIdentifier);
            if ($redirectPageId > 0 && $this->context->getAspect('frontend.user')->isLoggedIn()) {
                $redirectUri = $this->uriBuilder->reset()
                                                 ->setTargetPageUid($redirectPageId)
                                                 ->setCreateAbsoluteUri(true)
                                                 ->build();

                return new RedirectResponse($redirectUri, 307);
            }
        }

        return $handler->handle($request);
    }
}
