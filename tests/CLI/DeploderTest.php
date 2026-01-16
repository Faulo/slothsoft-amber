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
    public function test_load_imploded(string $in): void {
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        $this->assertTrue(true);
    }
    
    /**
     *
     * @dataProvider fileProvider
     */
    public function test_load_deploded(string $in, string $out): void {
        $outFile = FileInfoFactory::createFromPath($out);
        
        $sut = new Deploder();
        $sut->load($outFile);
        
        $this->assertTrue(true);
    }
    
    /**
     *
     * @dataProvider fileProvider
     * @depends test_load_imploded
     */
    public function test_getRealHunkCount(string $in, string $out, int $hunkCount): void {
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        $actual = $sut->getRealHunkCount();
        
        $this->assertThat($actual, new IsEqual($hunkCount));
    }
    
    /**
     *
     * @dataProvider fileProvider
     * @depends test_load_imploded
     */
    public function test_save_imploded(string $in, string $out, int $hunkCount): void {
        $inFile = FileInfoFactory::createFromPath($in);
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        $sut->save($actualFile);
        
        $this->assertFileEquals($in, (string) $actualFile);
    }
    
    /**
     *
     * @dataProvider fileProvider
     * @depends test_load_deploded
     */
    public function test_save_deploded(string $in, string $out, int $hunkCount): void {
        $outFile = FileInfoFactory::createFromPath($out);
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = new Deploder();
        $sut->load($outFile);
        
        $sut->save($actualFile);
        
        $this->assertFileEquals($out, (string) $actualFile);
    }
    
    /**
     *
     * @dataProvider fileProvider
     * @depends test_save_deploded
     */
    public function test_deplode(string $in, string $out, int $hunkCount): void {
        $inFile = FileInfoFactory::createFromPath($in);
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        $sut->deplode();
        
        $sut->save($actualFile);
        
        $this->assertFileEquals($out, (string) $actualFile);
    }
    
    public function fileProvider(): iterable {
        yield 'AM2_BLIT' => [
            'test-files/Amberfiles/AM2_BLIT.imploded',
            'test-files/Amberfiles/AM2_BLIT.exploded',
            11
        ];
    }
}