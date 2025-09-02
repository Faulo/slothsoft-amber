<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;

/**
 * AmbToolTest
 *
 * @see AmbTool
 *
 * @todo auto-generated
 */
class AmbToolTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmbTool::class), "Failed to load class 'Slothsoft\Amber\CLI\AmbTool'!");
    }
}