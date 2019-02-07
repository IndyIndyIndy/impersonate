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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles logging in a frontend user with the given uid
 */
class FrontendLoginController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function loginAction(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // @todo do login
        $uid = (int) $request->getParsedBody()['uid'];

        if (!empty($uid)) {
            $response->getBody()->write(json_encode([
                'response' => true
            ]));
            return $response;
        }
        $response->getBody()->write(json_encode([
            'error' => 'TODO'
        ]));
        return $response;
    }

}