<?php

namespace ChristianEssl\Impersonate\Middleware;

use ChristianEssl\Impersonate\Service\ConfigurationService;
use ChristianEssl\Impersonate\Utility\VerificationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class RedirectHandler implements MiddlewareInterface
{
    public function __construct(
        protected readonly Context $context,
        protected readonly ConfigurationService $configurationService,
        protected readonly SiteFinder $siteFinder
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
            $getRedirectPageUri = $this->configurationService->getRedirectPageUri($siteIdentifier);
            if ($getRedirectPageUri !== '' && $this->context->getAspect('frontend.user')->isLoggedIn()) {
                return new RedirectResponse($getRedirectPageUri, 307);
            }
        }

        return $handler->handle($request);
    }
}
