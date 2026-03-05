<?php

namespace BDCConecta\Services;

class EntityService extends AbstractService
{
    /**
     * Get entity data
     * POST /global/data/get-entity
     */
    public function getEntity(array $data): array
    {
        return $this->post('/global/data/get-entity', $data);
    }
}
