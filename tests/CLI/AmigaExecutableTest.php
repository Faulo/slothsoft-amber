<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\FarahTesting\TestUtils;
use Throwable;

/**
 * AmigaExecutableTest
 *
 * @see AmigaExecutable
 */
final class AmigaExecutableTest extends TestCase {
    
    private const AM2_CPU_IMPLODED = 'test-files/Amberfiles/AM2_CPU.imploded';
    
    private const AM2_CPU_DEPLODED = 'test-files/Amberfiles/AM2_CPU.deploded';
    
    private const AM2_BLIT_IMPLODED = 'test-files/Amberfiles/AM2_BLIT.imploded';
    
    private const AM2_BLIT_DEPLODED = 'test-files/Amberfiles/AM2_BLIT.deploded';
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmigaExecutable::class), "Failed to load class 'Slothsoft\Amber\CLI\AmigaExecutable'!");
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
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        $this->assertTrue(true);
    }
    
    /**
     *
     * @dataProvider fileProvider
     */
    public function test_load_deploded(string $in, string $out): void {
        $outFile = FileInfoFactory::createFromPath($out);
        
        $sut = new AmigaExecutable();
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
        
        $sut = new AmigaExecutable();
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
        
        $sut = new AmigaExecutable();
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
        
        $sut = new AmigaExecutable();
        $sut->load($outFile);
        
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
        
        $in = self::AM2_CPU_IMPLODED;
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new AmigaExecutable();
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
        $in = self::AM2_CPU_IMPLODED;
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        try {
            $sut->deplode();
        } catch (Throwable $e) {}
        
        $this->assertThat($sut->firstLiteralLength, new IsEqual(60));
        $this->assertThat($sut->initialBitBuffer, new IsEqual(162));
        $this->assertThat($sut->dataSize, new IsEqual(160241));
    }
    
    /**
     *
     * @depends test_deplode_metadata
     */
    public function test_deplode_table(): void {
        $matchBase = [];
        $matchBase[] = 64;
        $matchBase[] = 128;
        $matchBase[] = 128;
        $matchBase[] = 256;
        $matchBase[] = 192;
        $matchBase[] = 640;
        $matchBase[] = 1152;
        $matchBase[] = 2304;
        
        $matchExtra = [];
        $matchExtra[] = 6;
        $matchExtra[] = 7;
        $matchExtra[] = 7;
        $matchExtra[] = 128;
        $matchExtra[] = 7;
        $matchExtra[] = 129;
        $matchExtra[] = 130;
        $matchExtra[] = 131;
        $matchExtra[] = 128;
        $matchExtra[] = 131;
        $matchExtra[] = 133;
        $matchExtra[] = 134;
        
        $in = self::AM2_CPU_IMPLODED;
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        try {
            $sut->deplode();
        } catch (Throwable $e) {}
        
        $this->assertThat($sut->matchBase, new IsEqual($matchBase));
        $this->assertThat($sut->matchExtra, new IsEqual($matchExtra));
    }
    
    /**
     *
     * @depends test_deplode_table
     */
    public function test_deplode_deplodedSize(): void {
        $deplodedSize = 347940;
        
        $in = self::AM2_CPU_IMPLODED;
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        try {
            $sut->deplode();
        } catch (Throwable $e) {
            trigger_error((string) $e, E_USER_WARNING);
        }
        
        $this->assertThat($sut->deplodedSize, new IsEqual($deplodedSize));
    }
    
    /**
     *
     * @dataProvider fileProvider
     * @depends test_deplode_deplodedSize
     */
    public function test_deplode(string $in, string $out, int $hunkCount): void {
        $inFile = FileInfoFactory::createFromPath($in);
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        $sut->deplode();
        
        $sut->save($actualFile);
        
        $this->assertFileEquals($out, (string) $actualFile);
    }
    
    public function fileProvider(): iterable {
        yield 'AM2_CPU' => [
            self::AM2_CPU_IMPLODED,
            self::AM2_CPU_DEPLODED,
            10
        ];
        yield 'AM2_BLIT' => [
            self::AM2_BLIT_IMPLODED,
            self::AM2_BLIT_DEPLODED,
            11
        ];
    }
}