<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Module;

/**
 * AmbToolTest
 *
 * @see AmbTool
 */
class AmbToolTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmbTool::class), "Failed to load class 'Slothsoft\Amber\CLI\AmbTool'!");
    }

    /**
     *
     * @dataProvider archiveProvider
     * @test
     */
    public function testExtract(string $inFile, string $expectedDirectory) {
        $actualDirectory = temp_dir(__CLASS__);

        $sut = self::init();
        $sut->extractArchive(FileInfoFactory::createFromPath($inFile), FileInfoFactory::createFromPath($actualDirectory));

        $this->assertEquals(FileSystem::scanDir($expectedDirectory), FileSystem::scanDir($actualDirectory));
    }

    public static function archiveProvider(): array {
        return PHP_OS_FAMILY === 'Windows' ? [
            '2Icon_gfx.amb' => [
                'test-files/2Icon_gfx/2Icon_gfx.amb',
                'test-files/2Icon_gfx/archive'
            ]
        ] : [];
    }

    private static function init(): AmbTool {
        static $sut = null;
        if ($sut === null) {
            $sut = new AmbTool(self::getPath());
        }
        return $sut;
    }

    private static function getPath(): string {
        return (string) Module::resolveToAsset(FarahUrl::createFromReference('farah://slothsoft@amber/cli/ambtool'))->getFileInfo();
    }
}