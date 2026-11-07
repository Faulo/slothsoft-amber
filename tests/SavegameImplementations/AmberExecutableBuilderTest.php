<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\SavegameImplementations;

use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;
use Slothsoft\Amber\CLI\AmigaExecutable;
use Slothsoft\Amber\CLI\AmigaExecutableTest;
use Slothsoft\Amber\CLI\Hunk;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\FarahTesting\TestUtils;

/**
 * AmberExecutableBuilderTest
 *
 * @see AmberExecutableBuilder
 */
final class AmberExecutableBuilderTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmberExecutableBuilder::class), "Failed to load class 'Slothsoft\Amber\SavegameImplementations\AmberExecutableBuilder'!");
    }
    
    protected function setUp(): void {
        TestUtils::changeWorkingDirectoryToComposerRoot();
    }
    
    public function getArchives(): iterable {
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
     * @param string $archive
     */
    public function test_buildArchive(string $path, string $archive): void {
        $files = $this->createFiles($path, $archive);
        $sut = new AmberExecutableBuilder(FileInfoFactory::createFromPath('assets/source/ambermoon/Thalion-v1.05-DE'));
        $actual = $sut->buildArchive($files);
        
        $this->assertThat($actual, new IsEqual(file_get_contents($archive)));
    }
    
    /** @dataProvider getArchives */
    public function testRepeatedBuildsDoNotRetainPreviousEdits(string $path, string $archive): void {
        $files = $this->createFiles($path, $archive);
        $modified = clone $files[0];
        $modified->content[0] = chr(ord($modified->content[0]) ^ 0xff);
        $sut = new AmberExecutableBuilder(FileInfoFactory::createFromPath('assets/source/ambermoon/Thalion-v1.05-DE'));
        self::assertNotSame(file_get_contents($archive), $sut->buildArchive([$modified]));
        self::assertSame(file_get_contents($archive), $sut->buildArchive(array_slice($files, 1)));
    }
    
    private function createFiles(string $path, string $archive): array {
        $executable = new AmigaExecutable();
        $executable->load(FileInfoFactory::createFromPath($archive));
        
        $files = [];
        
        $codeIndex = 1;
        $dataIndex = 1;
        /** @var Hunk $hunk */
        foreach ($executable->getRealHunks() as $hunk) {
            switch ($hunk->type) {
                case Hunk::TYPE_CODE:
                    $file = new FileStub();
                    $file->fileName = 'CODE-' . $codeIndex++;
                    $file->content = $hunk->data;
                    $file->archivePath = $path;
                    $files[] = $file;
                    break;
                case Hunk::TYPE_DATA:
                    $file = new FileStub();
                    $file->fileName = 'DATA-' . $dataIndex++;
                    $file->content = $hunk->data;
                    $file->archivePath = $path;
                    $files[] = $file;
                    break;
            }
        }
        
        return $files;
    }
}
