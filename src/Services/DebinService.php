<?php

namespace BDCConecta\Services;

class DebinService extends AbstractService
{
    /**
     * Attach buyer
     * POST /buyer-attachment
     */
    public function attachBuyer(array $data): array
    {
        return $this->post('/buyer-attachment', $data);
    }

    /**
     * Get buyer by CUIT
     * POST /buyer-by-cuit
     */
    public function getBuyerByCuit(array $data): array
    {
        return $this->post('/buyer-by-cuit', $data);
    }

    /**
     * Remove buyer account
     * POST /buyer-remove-account
     */
    public function removeBuyerAccount(array $data): array
    {
        return $this->post('/buyer-remove-account', $data);
    }

    /**
     * Confirm DEBIN
     * POST /confirm-debin
     */
    public function confirmDebin(array $data): array
    {
        return $this->post('/confirm-debin', $data);
    }

    /**
     * Create DEBIN
     * POST /debin-create
     */
    public function createDebin(array $data): array
    {
        return $this->post('/debin-create', $data);
    }

    /**
     * Remove DEBIN
     * POST /debin-remove
     */
    public function removeDebin(array $data): array
    {
        return $this->post('/debin-remove', $data);
    }

    /**
     * List DEBINs
     * POST /debin-list
     */
    public function listDebins(array $filters = []): array
    {
        return $this->post('/debin-list', $filters);
    }

    /**
     * Validate CUIT
     * POST /validate-cuit
     */
    public function validateCuit(array $data): array
    {
        return $this->post('/validate-cuit', $data);
    }

    /**
     * Attach seller
     * POST /seller-attachment
     */
    public function attachSeller(array $data): array
    {
        return $this->post('/seller-attachment', $data);
    }

    /**
     * Get seller by CUIT
     * POST /seller-by-cuit
     */
    public function getSellerByCuit(array $data): array
    {
        return $this->post('/seller-by-cuit', $data);
    }

    /**
     * Remove seller account
     * POST /seller-remove-account
     */
    public function removeSellerAccount(array $data): array
    {
        return $this->post('/seller-remove-account', $data);
    }
}
