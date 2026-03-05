<?php

namespace BDCConecta\Enums;

/**
 * Sub-account status enum
 *
 * Status transitions:
 * - ACTIVE → SUSPENDED ✓
 * - ACTIVE → BLOCKED ✓
 * - SUSPENDED → ACTIVE ✓
 * - SUSPENDED → BLOCKED ✓
 * - BLOCKED → (none) ✗ (permanent)
 *
 * Only ACTIVE accounts can operate.
 */
enum SubAccountStatus: string
{
    case ACTIVE = 'ACTIVE';
    case SUSPENDED = 'SUSPENDED';
    case BLOCKED = 'BLOCKED';

    /**
     * Get valid status transitions from current status
     */
    public function getAllowedTransitions(): array
    {
        return match($this) {
            self::ACTIVE => [self::SUSPENDED, self::BLOCKED],
            self::SUSPENDED => [self::ACTIVE, self::BLOCKED],
            self::BLOCKED => [], // No transitions allowed from BLOCKED
        };
    }

    /**
     * Check if transition to target status is valid
     */
    public function canTransitionTo(SubAccountStatus $target): bool
    {
        return in_array($target, $this->getAllowedTransitions());
    }

    /**
     * Check if this status allows operations
     */
    public function canOperate(): bool
    {
        return $this === self::ACTIVE;
    }
}
