<?php

namespace BDCConecta\Auth;

class TokenStorage
{
    private ?string $accessToken = null;
    private ?int $expiresAt = null;

    public function setToken(string $token, int $expiresIn): void
    {
        $this->accessToken = $token;
        $this->expiresAt = time() + $expiresIn;
    }

    public function getToken(): ?string
    {
        if ($this->isExpired()) {
            return null;
        }
        return $this->accessToken;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return true;
        }

        // Consider token expired 60 seconds before actual expiration
        return time() >= ($this->expiresAt - 60);
    }

    public function clear(): void
    {
        $this->accessToken = null;
        $this->expiresAt = null;
    }
}
