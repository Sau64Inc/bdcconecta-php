<?php

namespace BDCConecta\Services;

class AccountsService extends AbstractService
{
    /**
     * List all accounts
     * GET /accounts
     */
    public function list(): array
    {
        return $this->get('/accounts');
    }

    /**
     * Get account information by CBU, CVU or Alias
     * GET /accounts/info/{CBU_CVU_ALIAS}
     */
    public function getInfo(string $cbuCvuAlias): array
    {
        return $this->get("/accounts/info/{$cbuCvuAlias}");
    }
}
