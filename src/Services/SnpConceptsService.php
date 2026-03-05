<?php

namespace BDCConecta\Services;

class SnpConceptsService extends AbstractService
{
    /**
     * Get SNP concepts
     * GET /get-snp-concepts
     */
    public function getConcepts(): array
    {
        return $this->get('/get-snp-concepts');
    }
}
