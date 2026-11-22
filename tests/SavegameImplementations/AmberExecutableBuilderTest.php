<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\SavegameImplementations;

use Generator;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;
use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Amber\CLI\AmigaExecutableTest;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\FarahTesting\TestUtils;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\EditorConfig;
use Slothsoft\Savegame\Node\FileContainer;
use SplFileInfo;

/**
 * AmberExecutableBuilderTest
 *
 * @see AmberExecutableBuilder
 */
final class AmberExecutableBuilderTest extends TestCase {
    
    private const SOURCE_DIRECTORY = 'assets/source/ambermoon/Thalion-v1.05-DE';
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmberExecutableBuilder::class), "Failed to load class 'Slothsoft\Amber\SavegameImplementations\AmberExecutableBuilder'!");
    }
    
    protected function setUp(): void {
        TestUtils::changeWorkingDirectoryToComposerRoot();
    }
    
    /** @return Generator<string, array{string, string}> */
    public function getArchives(): Generator {
        yield 'AM2_BLIT' => [
            'Amberfiles/AM2_BLIT',
            AmigaExecutableTest::AM2_BLIT_DEPLODED
        ];
        yield 'AM2_CPU' => [
            'Amberfiles/AM2_CPU',
            AmigaExecutableTest::AM2_CPU_DEPLODED
        ];
    }
    
    /**
     *
     * @dataProvider getArchives
     */
    public function test_buildArchive(string $path, string $archive): void {
        $files = $this->loadFiles($path);
        $sut = new AmberExecutableBuilder(new SplFileInfo(self::SOURCE_DIRECTORY));
        $actual = $sut->buildArchive($files);
        
        $this->assertThat($actual, new IsEqual(file_get_contents($archive)));
    }
    
    /** @dataProvider getArchives */
    public function testRepeatedBuildsDoNotRetainPreviousEdits(string $path, string $archive): void {
        $files = $this->loadFiles($path);
        $modified = $files[0];
        $content = $modified->getContent();
        $content[0] = chr(ord($content[0]) ^ 0xff);
        $modified->setContent($content);
        $sut = new AmberExecutableBuilder(new SplFileInfo(self::SOURCE_DIRECTORY));
        self::assertNotSame(file_get_contents($archive), $sut->buildArchive([$modified]));
        self::assertSame(file_get_contents($archive), $sut->buildArchive(array_slice($files, 1)));
    }
    
    /** @return list<FileContainer> */
    private function loadFiles(string $archivePath): array {
        $namespace = DOMHelper::NS_SAVEGAME_EDITOR;
        $xml = <<<XML
            <savegame xmlns="$namespace">
                <archive path="$archivePath" type="AM2">
                    <for-each-file />
                </archive>
            </savegame>
            XML;
        $infoset = FileInfoFactory::createFromString($xml);
        
        $directory = FileInfoFactory::createTempFile();
        $editor = new Editor(new EditorConfig(new SplFileInfo(self::SOURCE_DIRECTORY), $directory, $directory, $infoset, [
            AmbTool::TYPE_AM2 => new AmberExecutableExtractor()
        ], []));
        return iterator_to_array($editor->loadArchive($archivePath)->getFileNodes(), false);
    }
}
