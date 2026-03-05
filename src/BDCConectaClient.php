<?php

namespace BDCConecta;

use BDCConecta\Http\Client;
use BDCConecta\Auth\AuthManager;
use BDCConecta\Services;

class BDCConectaClient
{
    private Client $httpClient;
    private AuthManager $authManager;
    private array $services = [];

    public function __construct(
        private readonly Configuration $config
    ) {
        $this->httpClient = new Client($config);
        $this->authManager = new AuthManager($config, $this->httpClient);
    }

    // Service accessors (lazy-loaded)
    public function accounts(): Services\AccountsService
    {
        return $this->getService(Services\AccountsService::class);
    }

    public function cvuAccounts(): Services\CvuAccountsService
    {
        return $this->getService(Services\CvuAccountsService::class);
    }

    public function cvuAlias(): Services\CvuAliasService
    {
        return $this->getService(Services\CvuAliasService::class);
    }

    public function movements(): Services\MovementsService
    {
        return $this->getService(Services\MovementsService::class);
    }

    public function transfers(): Services\TransferService
    {
        return $this->getService(Services\TransferService::class);
    }

    public function webhooks(): Services\WebhookService
    {
        return $this->getService(Services\WebhookService::class);
    }

    public function debin(): Services\DebinService
    {
        return $this->getService(Services\DebinService::class);
    }

    public function currency(): Services\CurrencyService
    {
        return $this->getService(Services\CurrencyService::class);
    }

    public function banks(): Services\BanksService
    {
        return $this->getService(Services\BanksService::class);
    }

    public function entity(): Services\EntityService
    {
        return $this->getService(Services\EntityService::class);
    }

    public function cbuAlias(): Services\CbuAliasService
    {
        return $this->getService(Services\CbuAliasService::class);
    }

    public function snpConcepts(): Services\SnpConceptsService
    {
        return $this->getService(Services\SnpConceptsService::class);
    }

    public function coelsa(): Services\CoelsaService
    {
        return $this->getService(Services\CoelsaService::class);
    }

    // Debug
    public function enableDebug(): self
    {
        $this->config->setDebug(true);
        return $this;
    }

    public function disableDebug(): self
    {
        $this->config->setDebug(false);
        return $this;
    }

    public function getLastDebugInfo(): ?Http\DebugInfo
    {
        return $this->httpClient->getLastDebugInfo();
    }

    // Utility methods
    public function healthcheck(): array
    {
        return $this->httpClient->get('/healthcheck')->json();
    }

    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    public function getAuthManager(): AuthManager
    {
        return $this->authManager;
    }

    private function getService(string $serviceClass): object
    {
        if (!isset($this->services[$serviceClass])) {
            $this->services[$serviceClass] = new $serviceClass($this);
        }

        return $this->services[$serviceClass];
    }
}
