<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Module;

/**
 * AmbGfxTest
 *
 * @see AmbGfx
 */
class AmbGfxTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmbGfx::class), "Failed to load class 'Slothsoft\Amber\CLI\AmbGfx'!");
    }
    
    /**
     *
     * @dataProvider gfxProvider
     * @test
     */
    public function testExtract(string $inFile, string $expectedFile, int $width, int $bitplanes, int $offset, int $size, int $palette) {
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = self::init();
        $sut->extractTga(FileInfoFactory::createFromPath($inFile), $actualFile, $width, $bitplanes, $offset, $size, $palette);
        
        $this->assertFileEquals($expectedFile, (string) $actualFile);
    }
    
    public static function gfxProvider(): array {
        return AmbGfx::isSupported() ? [
            '2Icon_gfx.amb/003/1' => [
                'test-files/2Icon_gfx/archive/003',
                'test-files/2Icon_gfx/ambgfx/003/0/49.tga',
                16,
                5,
                0,
                160,
                49
            ],
            '2Icon_gfx.amb/003/2' => [
                'test-files/2Icon_gfx/archive/003',
                'test-files/2Icon_gfx/ambgfx/003/0/49.tga',
                16,
                5,
                0,
                160,
                49
            ],
            '2Icon_gfx.amb/004/1' => [
                'test-files/2Icon_gfx/archive/004',
                'test-files/2Icon_gfx/ambgfx/004/0/6.tga',
                16,
                5,
                0,
                160,
                6
            ]
        ] : [];
    }
    
    private static function init(): AmbGfx {
        static $sut = null;
        if ($sut === null) {
            $sut = new AmbGfx(self::getPath());
        }
        return $sut;
    }
    
    private static function getPath(): string {
        return (string) Module::resolveToAsset(FarahUrl::createFromReference('farah://slothsoft@amber/cli/amgfx'))->getFileInfo();
    }
}