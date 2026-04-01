<?php

namespace Tests\Unit;

use App\Services\PurchaseOrderService;
use PHPUnit\Framework\TestCase;

class PurchaseOrderSerialParsingTest extends TestCase
{
    /**
     * Test that serial numbers with hyphens are NOT expanded as ranges.
     */
    public function test_serial_numbers_with_hyphens_are_treated_as_literal(): void
    {
        $inventoryService = $this->createMock(\App\Services\InventoryService::class);
        $service = new PurchaseOrderService($inventoryService);

        $input = 'SN-123-ABC, SN-456-DEF';
        $parsed = $service->parseSerialNumbers($input);

        $this->assertCount(2, $parsed);
        $this->assertEquals(['SN-123-ABC', 'SN-456-DEF'], array_values($parsed));
    }

    /**
     * Test that traditional range syntax is no longer expanded.
     */
    public function test_traditional_range_syntax_is_not_expanded(): void
    {
        $inventoryService = $this->createMock(\App\Services\InventoryService::class);
        $service = new PurchaseOrderService($inventoryService);

        $input = 'SN001-SN003';
        $parsed = $service->parseSerialNumbers($input);

        // Before it would have been SN001, SN002, SN003. Now it's just SN001-SN003 if it's one tag,
        // or SN001 and SN003 if split by comma. But our split logic splits by [,\s\n]+.
        // So SN001-SN003 should remain a single string if no spaces or commas are inside it.

        $this->assertCount(1, $parsed);
        $this->assertEquals(['SN001-SN003'], array_values($parsed));
    }

    /**
     * Test array input (simulating Select2 tags).
     */
    public function test_array_input_is_parsed_correctly(): void
    {
        $inventoryService = $this->createMock(\App\Services\InventoryService::class);
        $service = new PurchaseOrderService($inventoryService);

        $input = ['SN-001', 'SN-002', 'SN-003'];
        $parsed = $service->parseSerialNumbers($input);

        $this->assertCount(3, $parsed);
        $this->assertEquals(['SN-001', 'SN-002', 'SN-003'], array_values($parsed));
    }

    /**
     * Test mixed comma and space separation.
     */
    public function test_mixed_separators(): void
    {
        $inventoryService = $this->createMock(\App\Services\InventoryService::class);
        $service = new PurchaseOrderService($inventoryService);

        $input = "SN-001, SN-002 SN-003\nSN-004";
        $parsed = $service->parseSerialNumbers($input);

        $this->assertCount(4, $parsed);
        $this->assertEquals(['SN-001', 'SN-002', 'SN-003', 'SN-004'], array_values($parsed));
    }
}
