<?php

namespace BDCConecta\Services;

class CurrencyService extends AbstractService
{
    /**
     * Get currency exchange rates reference
     * GET /global/data/currencies-rate-reference
     */
    public function getRateReference(): array
    {
        return $this->get('/global/data/currencies-rate-reference');
    }
}
