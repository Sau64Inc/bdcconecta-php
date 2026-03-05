<?php

namespace BDCConecta\Services;

use BDCConecta\BDCConectaClient;
use BDCConecta\Http\Client;
use BDCConecta\Auth\AuthManager;
use BDCConecta\Http\Response;

abstract class AbstractService
{
    protected Client $httpClient;
    protected AuthManager $authManager;

    public function __construct(
        protected readonly BDCConectaClient $client
    ) {
        $this->httpClient = $client->getHttpClient();
        $this->authManager = $client->getAuthManager();
    }

    protected function request(string $method, string $path, array $options = []): array
    {
        // Add authorization header
        $options['headers'] = array_merge(
            $options['headers'] ?? [],
            $this->buildAuthHeaders()
        );

        $response = $this->httpClient->request($method, $path, $options);

        return $response->json();
    }

    protected function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    protected function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, ['json' => $data]);
    }

    protected function patch(string $path, array $data = []): array
    {
        return $this->request('PATCH', $path, ['json' => $data]);
    }

    protected function put(string $path, array $data = []): array
    {
        return $this->request('PUT', $path, ['json' => $data]);
    }

    protected function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    protected function buildAuthHeaders(): array
    {
        $token = $this->authManager->getAccessToken();

        return [
            'Authorization' => 'Bearer ' . $token
        ];
    }
}
