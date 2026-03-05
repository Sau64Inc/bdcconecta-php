<?php

namespace BDCConecta\Services;

class BanksService extends AbstractService
{
    /**
     * Get list of banks
     * GET /banks
     */
    public function list(): array
    {
        return $this->get('/banks');
    }
}
