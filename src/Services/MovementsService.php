<?php

namespace BDCConecta\Services;

class MovementsService extends AbstractService
{
    /**
     * Get account movements
     * POST /movements/{CBU_CVU_ALIAS}
     */
    public function getMovements(string $cbuCvuAlias, array $filters = []): array
    {
        return $this->post("/movements/{$cbuCvuAlias}", $filters);
    }
}
