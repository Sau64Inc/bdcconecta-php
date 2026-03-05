<?php

namespace BDCConecta\Services;

class CbuAliasService extends AbstractService
{
    /**
     * Create CBU alias
     * POST /cbu-alias
     */
    public function create(array $data): array
    {
        return $this->post('/cbu-alias', $data);
    }

    /**
     * Update CBU alias
     * PATCH /cbu-alias
     */
    public function update(array $data): array
    {
        return $this->patch('/cbu-alias', $data);
    }

    /**
     * Delete CBU alias
     * DELETE /cbu-alias/{alias}
     */
    public function delete(string $alias): array
    {
        return $this->request('DELETE', "/cbu-alias/{$alias}");
    }

    /**
     * Get CBU alias
     * GET /cbu-alias/{cbu}
     */
    public function getAlias(string $cbu): array
    {
        return $this->request('GET', "/cbu-alias/{$cbu}");
    }
}
