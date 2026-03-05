<?php

namespace BDCConecta\Services;

class CvuAliasService extends AbstractService
{
    /**
     * Update CVU alias
     * PATCH /cvu-alias
     */
    public function update(array $data): array
    {
        return $this->patch('/cvu-alias', $data);
    }
}
