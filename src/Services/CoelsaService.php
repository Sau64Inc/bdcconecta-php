<?php

namespace BDCConecta\Services;

class CoelsaService extends AbstractService
{
    /**
     * Get COELSA additional data
     * GET /get-coelsa-aditional-data/{idCoelsa}
     */
    public function getAdditionalData(string $idCoelsa): array
    {
        return $this->get("/get-coelsa-aditional-data/{$idCoelsa}");
    }
}
