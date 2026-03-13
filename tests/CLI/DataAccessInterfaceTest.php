<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;

/**
 * DataAccessInterfaceTest
 *
 * @see DataAccessInterface
 *
 * @todo auto-generated
 */
final class DataAccessInterfaceTest extends TestCase {
    
    public function testInterfaceExists(): void {
        $this->assertTrue(interface_exists(DataAccessInterface::class), "Failed to load interface 'Slothsoft\Amber\CLI\DataAccessInterface'!");
    }
}