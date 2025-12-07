<?php

declare(strict_types=1);

namespace Test\CompatibilityPackage;

/**
 * Simple test service for compatibility testing.
 */
class TestService
{
    /**
     * Test method that returns true.
     */
    public function test(): bool
    {
        return true;
    }

    /**
     * Get service name.
     */
    public function getName(): string
    {
        return 'TestService';
    }
}

