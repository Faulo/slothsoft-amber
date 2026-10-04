<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\Assets;

use PHPUnit\Framework\TestCase;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Module;

/**
 * GfxBuilderTest
 *
 * @see GfxBuilder
 *
 */
final class GfxBuilderTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(GfxBuilder::class), "Failed to load class 'Slothsoft\Amber\Assets\GfxBuilder'!");
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testCreatesGraphicAsset(): void {
        $url = FarahUrl::createFromReference('farah://slothsoft@amber/api/gfx?archivePath=Amberfiles/2Icon_gfx.amb&fileId=004&paletteId=6&gfxId=0');
        $file = Module::resolveToFileWriter($url)->toFile();
        $image = getimagesize((string) $file);
        
        $this->assertIsArray($image);
        $this->assertSame('image/png', $image['mime']);
        $this->assertSame(16, $image[0]);
        $this->assertSame(16, $image[1]);
    }
}
