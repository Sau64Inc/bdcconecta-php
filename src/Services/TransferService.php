<?php

namespace BDCConecta\Services;

class TransferService extends AbstractService
{
    /**
     * Create transfer request
     * POST /movements/transfer-request
     */
    public function createTransferRequest(array $transferData): array
    {
        return $this->post('/movements/transfer-request', $transferData);
    }

    /**
     * Get transfer request status
     * GET /movements/transfer-request/{originId}
     */
    public function getTransferRequest(string $originId): array
    {
        return $this->get("/movements/transfer-request/{$originId}");
    }
}
