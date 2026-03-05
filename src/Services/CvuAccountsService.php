<?php

namespace BDCConecta\Services;

use BDCConecta\Enums\SubAccountStatus;
use BDCConecta\Enums\SortDirection;

class CvuAccountsService extends AbstractService
{
    /**
     * Get CVU sub-accounts list
     * POST /accounts/get-cvu-accounts
     *
     * @param string $cbu CBU of the parent account (22 digits, required)
     * @param int|null $pageOffset Page offset for pagination (optional)
     * @param int|null $pageSize Number of results per page (optional)
     * @param SortDirection|null $sortDirection Sort direction: ASC or DESC (optional)
     * @param string|null $sortField Field to sort by, e.g., "fecha_creacion" (optional)
     * @param string|null $startCreatedDate Filter start date in format YYYY-MM-DD (optional)
     * @param string|null $endCreatedDate Filter end date in format YYYY-MM-DD (optional)
     * @return array
     */
    public function getCvuAccounts(
        string $cbu,
        ?int $pageOffset = null,
        ?int $pageSize = null,
        ?SortDirection $sortDirection = null,
        ?string $sortField = null,
        ?string $startCreatedDate = null,
        ?string $endCreatedDate = null
    ): array {
        $data = ['cbu' => $cbu];

        if ($pageOffset !== null) {
            $data['pageOffset'] = $pageOffset;
        }

        if ($pageSize !== null) {
            $data['pageSize'] = $pageSize;
        }

        if ($sortDirection !== null) {
            $data['sortDirection'] = $sortDirection->value;
        }

        if ($sortField !== null) {
            $data['sortField'] = $sortField;
        }

        if ($startCreatedDate !== null) {
            $data['startCreatedDate'] = $startCreatedDate;
        }

        if ($endCreatedDate !== null) {
            $data['endCreatedDate'] = $endCreatedDate;
        }

        return $this->post('/accounts/get-cvu-accounts', $data);
    }

    /**
     * Create sub-account
     * POST /sub-account
     *
     * @param string $originId Unique ID for tracking (required)
     * @param string $cbu CBU of the parent account (22 digits, required)
     * @param string $label Alias for the sub-account (required)
     * @param string $currency Currency code: "032" (ARS), "840" (USD), etc. (required)
     * @return array
     */
    public function createSubAccount(
        string $originId,
        string $cbu,
        ?string $label = null,
        string $currency = '032'  // ARS by default
    ): array {
        $data = [
            'originId' => $originId,
            'cbu' => $cbu,
            'currency' => $currency
        ];

        // Only include label if provided
        if ($label !== null) {
            $data['label'] = $label;
        }

        return $this->post('/sub-account', $data);
    }

    /**
     * Update sub-account status
     * PATCH /sub-account/{cvu}
     *
     * Status transitions:
     * - ACTIVE → SUSPENDED ✓
     * - ACTIVE → BLOCKED ✓
     * - SUSPENDED → ACTIVE ✓
     * - SUSPENDED → BLOCKED ✓
     * - BLOCKED → (none) ✗ (permanent)
     *
     * Only ACTIVE accounts can operate.
     *
     * @param string $cvu CVU or Account ID of the sub-account
     * @param SubAccountStatus $status New status (ACTIVE, SUSPENDED, or BLOCKED)
     * @return array
     */
    public function updateSubAccount(
        string $cvu,
        SubAccountStatus $status
    ): array {
        return $this->patch("/sub-account/{$cvu}", [
            'status' => $status->value
        ]);
    }

    /**
     * Get sub-account info
     * GET /sub-account/{originId}
     *
     * @param string $originId Origin ID of the sub-account
     * @return array
     */
    public function getSubAccount(string $originId): array
    {
        return $this->get("/sub-account/{$originId}");
    }
}
