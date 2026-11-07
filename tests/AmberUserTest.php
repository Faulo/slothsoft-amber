<?php
declare(strict_types = 1);

namespace Slothsoft\Amber;

use PHPUnit\Framework\TestCase;

/**
 * AmberUserTest
 *
 * @see AmberUser
 *
 */
final class AmberUserTest extends TestCase {
    /** @runInSeparateProcess */
    public function testExplicitUsersArePreserved(): void {
        self::assertSame('alice', AmberUser::getNewIdIfDefault('alice'));
        self::assertSame('bob', AmberUser::getNewIdIfDefault('bob'));
        $generated = AmberUser::getNewIdIfDefault(AmberUser::DEFAULT_ID);
        self::assertNotSame(AmberUser::DEFAULT_ID, $generated);
        self::assertNotSame('alice', $generated);
        self::assertSame($generated, AmberUser::getNewIdIfDefault(AmberUser::DEFAULT_ID));
        self::assertSame('charlie', AmberUser::getNewIdIfDefault('charlie'));
    }
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmberUser::class), "Failed to load class 'Slothsoft\Amber\AmberUser'!");
    }
}
