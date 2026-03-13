<?php
declare(strict_types = 1);
namespace Slothsoft\Amber;

use PHPUnit\Framework\TestCase;

/**
 * AmberUtilsTest
 *
 * @see AmberUtils
 *
 * @todo auto-generated
 */
final class AmberUtilsTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmberUtils::class), "Failed to load class 'Slothsoft\Amber\AmberUtils'!");
    }
}