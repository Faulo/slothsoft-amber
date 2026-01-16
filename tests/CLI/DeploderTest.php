<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\FarahTesting\TestUtils;
use Throwable;
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
    
    /**
     *
     * @depends test_save_deploded
     */
    public function test_deplode_hunkSizes(): void {
        $hunkSizes = [];
        $hunkSizes[] = 183136;
        $hunkSizes[] = 84728;
        $hunkSizes[] = 5492;
        $hunkSizes[] = 15716;
        $hunkSizes[] = 87856;
        $hunkSizes[] = 59360;
        
        $in = 'test-files/Amberfiles/AM2_CPU.imploded';
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        try {
            $sut->deplode();
        } catch (Throwable $e) {}
        
        $this->assertThat($sut->deplodedHunkSizes, new IsEqual($hunkSizes));
    }
    
    /**
     *
     * @depends test_save_deploded
     */
    public function test_deplode_metadata(): void {
        $in = 'test-files/Amberfiles/AM2_CPU.imploded';
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new Deploder();
        $sut->load($inFile);
        
        try {
            $sut->deplode();
        } catch (Throwable $e) {}
        
        $this->assertThat($sut->firstLiteralLength, new IsEqual(60));
        $this->assertThat($sut->initialBitBuffer, new IsEqual(162));
        $this->assertThat($sut->dataSize, new IsEqual(160241));
    }
    
    public function fileProvider(): iterable {
        yield 'AM2_CPU' => [
            'test-files/Amberfiles/AM2_CPU.imploded',
            'test-files/Amberfiles/AM2_CPU.exploded',
            10
        ];
        yield 'AM2_BLIT' => [
            'test-files/Amberfiles/AM2_BLIT.imploded',
            'test-files/Amberfiles/AM2_BLIT.exploded',
            11
        ];
    }
}