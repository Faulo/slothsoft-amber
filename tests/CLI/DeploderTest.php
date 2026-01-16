<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\FarahTesting\TestUtils;

/**
 * DeploderTest
 *
 * @see Deploder
 */
final class DeploderTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Deploder::class), "Failed to load class 'Slothsoft\Amber\CLI\Deploder'!");
    }
    
    protected function setUp(): void {
        parent::setUp();
        TestUtils::changeWorkingDirectoryToComposerRoot();
    }
    
    /**
     * 
     * @dataProvider fileProvider
     */
    public function test_explode(string $in, string $expected): void {
        $inFile = FileInfoFactory::createFromPath($in);
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = new Deploder();
        
        $sut->explode($inFile, $actualFile);
        $actual = (string) $actualFile;
        
        $this->assertFileEquals($expected, $actual);
    }
    
    public function fileProvider(): iterable {
        yield 'AM2_BLIT' => [
            'test-files/Amberfiles/AM2_BLIT.imploded',
            'test-files/Amberfiles/AM2_BLIT.exploded'
        ];
    }
}