<?php

namespace BDCConecta\Auth;

use BDCConecta\Configuration;
use BDCConecta\Http\Client;
use BDCConecta\Exceptions\AuthenticationException;

class AuthManager
{
    private TokenStorage $tokenStorage;

    public function __construct(
        private readonly Configuration $config,
        private readonly Client $httpClient
    ) {
        $this->tokenStorage = new TokenStorage();
    }

    public function getAccessToken(): string
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            $token = $this->authenticate();
        }

        return $token;
    }

    public function authenticate(): string
    {
        try {

            // Call POST /auth with client credentials
            $response = $this->httpClient->post('/auth', [
                'clientId' => $this->config->getClientId(),
                'clientSecret' => $this->config->getClientSecret()
            ]);

            $responseData = $response->json();


            // Check if authentication was successful (statusCode = 0)
            if (!isset($responseData['statusCode']) || $responseData['statusCode'] !== 0) {
                $message = $responseData['message'] ?? 'Authentication failed';
                throw new AuthenticationException($message);
            }

            // Extract token from data object
            if (!isset($responseData['data']['accessToken']) || !isset($responseData['data']['expiresIn'])) {
                throw new AuthenticationException('Invalid authentication response: missing accessToken or expiresIn');
            }

            $accessToken = $responseData['data']['accessToken'];
            $expiresIn = $responseData['data']['expiresIn'];

            $this->tokenStorage->setToken($accessToken, $expiresIn);

            return $accessToken;

        } catch (AuthenticationException $e) {
            // Re-throw authentication exceptions
            throw $e;
        } catch (\Exception $e) {
            throw new AuthenticationException(
                'Authentication failed: ' . $e->getMessage(),
                0,
                ['originalException' => $e]
            );
        }
    }

    public function clearToken(): void
    {
        $this->tokenStorage->clear();
    }

    public function isAuthenticated(): bool
    {
        return $this->tokenStorage->getToken() !== null;
    }
}
