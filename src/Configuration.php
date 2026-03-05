<?php

namespace BDCConecta;

class Configuration
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $secretKey,
        private readonly string $baseUrl,
        private int $timeout = 30,
        private int $connectTimeout = 10,
        private bool $verifySSL = true,
        private bool $debug = false
    ) {}

    public static function production(string $clientId, string $clientSecret, string $secretKey): self
    {
        return new self(
            clientId: $clientId,
            clientSecret: $clientSecret,
            secretKey: $secretKey,
            baseUrl: 'https://api.bdcconecta.com'
        );
    }

    public static function sandbox(string $clientId, string $clientSecret, string $secretKey): self
    {
        return new self(
            clientId: $clientId,
            clientSecret: $clientSecret,
            secretKey: $secretKey,
            baseUrl: 'https://apihomo.bdcconecta.com',
			verifySSL: false,
        );
    }

    // Getters
    public function getClientId(): string { return $this->clientId; }
    public function getClientSecret(): string { return $this->clientSecret; }
    public function getSecretKey(): string { return $this->secretKey; }
    public function getBaseUrl(): string { return $this->baseUrl; }
    public function getTimeout(): int { return $this->timeout; }
    public function getConnectTimeout(): int { return $this->connectTimeout; }
    public function shouldVerifySSL(): bool { return $this->verifySSL; }
    public function isDebug(): bool { return $this->debug; }

    // Setters with fluent interface
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setConnectTimeout(int $connectTimeout): self
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    public function setVerifySSL(bool $verify): self
    {
        $this->verifySSL = $verify;
        return $this;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }
}
