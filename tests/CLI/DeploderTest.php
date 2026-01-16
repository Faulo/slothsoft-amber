<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\FarahTesting\TestUtils;
use PHPUnit\Framework\Constraint\IsEqual;

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
    public function test_load(string $in): void {
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new Deploder();
        $actual = $sut->load($inFile);
        
        $this->assertTrue($actual);
    }
    
    /**
     *
     * @dataProvider fileProvider
     * @depends test_load
     */
    public function test_getHunkCount(string $in, string $out, int $hunkCount): void {
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        $actual = $sut->getHunkCount();
        
        $this->assertThat($actual, new IsEqual($hunkCount));
    }
    
    /**
     *
     * @dataProvider fileProvider
     * @depends test_load
     */
    public function test_explode(string $in, string $out, int $hunkCount): void {
        $inFile = FileInfoFactory::createFromPath($in);
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        $sut->explode($actualFile);
        $actual = (string) $actualFile;
        
        $this->assertFileEquals($out, $actual);
    }
    
    public function fileProvider(): iterable {
        yield 'AM2_BLIT' => [
            'test-files/Amberfiles/AM2_BLIT.imploded',
            'test-files/Amberfiles/AM2_BLIT.exploded',
            11
        ];
    }
}