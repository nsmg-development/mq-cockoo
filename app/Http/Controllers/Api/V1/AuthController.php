<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ResponseTemplate;
use App\Services\Api\V1\AuthService;
use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends AccessTokenController
{
    use ResponseTemplate;

    private AuthService $authService;

    /**
     * Create a new controller instance.
     *
     * @param AuthorizationServer $server
     * @param TokenRepository $tokens
     * @param JwtParser $jwt
     * @param AuthService $authService
     */
    public function __construct(
        AuthorizationServer $server,
        TokenRepository $tokens,
        JwtParser $jwt,
        AuthService $authService
    )
    {
        $this->authService = $authService;

        parent::__construct($server, $tokens, $jwt);
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function issueToken(ServerRequestInterface $request): Response
    {
        $this->authService->pruneOldTokens($request);

        return $this->response(
            collect(
                json_decode(parent::issueToken($request)->getContent())
            )
        );
    }
}
