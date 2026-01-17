<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use Slothsoft\FarahTesting\TestUtils;
use Slothsoft\FarahTesting\Constraints\FileEqualsFile;
use Throwable;

/**
 * AmigaExecutableTest
 *
 * @see AmigaExecutable
 */
final class AmigaExecutableTest extends TestCase {
    
    private const AM2_CPU_IMPLODED = 'test-files/Amberfiles/AM2_CPU.imploded';
    
    private const AM2_CPU_DATA_IMPLODED = 'test-files/Amberfiles/AM2_CPU_DATA.imploded';
    
    private const AM2_CPU_DEPLODED = 'test-files/Amberfiles/AM2_CPU.deploded';
    
    private const AM2_CPU_DATA_DEPLODED = 'test-files/Amberfiles/AM2_CPU_DATA.deploded';
    
    private const AM2_BLIT_IMPLODED = 'test-files/Amberfiles/AM2_BLIT.imploded';
    
    private const AM2_BLIT_DEPLODED = 'test-files/Amberfiles/AM2_BLIT.deploded';
    
    private static AmigaExecutableDeplodeInfo $cpuInfo;
    
    private static function setUpCpuInfo(): void {
        if (! isset(self::$cpuInfo)) {
            self::$cpuInfo = new AmigaExecutableDeplodeInfo();
            self::$cpuInfo->firstLiteralLength = 60;
            self::$cpuInfo->initialBitBuffer = 162;
            self::$cpuInfo->implodedSize = 160241;
            
            self::$cpuInfo->matchBase[] = 64;
            self::$cpuInfo->matchBase[] = 128;
            self::$cpuInfo->matchBase[] = 128;
            self::$cpuInfo->matchBase[] = 256;
            self::$cpuInfo->matchBase[] = 192;
            self::$cpuInfo->matchBase[] = 640;
            self::$cpuInfo->matchBase[] = 1152;
            self::$cpuInfo->matchBase[] = 2304;
            
            self::$cpuInfo->matchExtra[] = 6;
            self::$cpuInfo->matchExtra[] = 7;
            self::$cpuInfo->matchExtra[] = 7;
            self::$cpuInfo->matchExtra[] = 128;
            self::$cpuInfo->matchExtra[] = 7;
            self::$cpuInfo->matchExtra[] = 129;
            self::$cpuInfo->matchExtra[] = 130;
            self::$cpuInfo->matchExtra[] = 131;
            self::$cpuInfo->matchExtra[] = 128;
            self::$cpuInfo->matchExtra[] = 131;
            self::$cpuInfo->matchExtra[] = 133;
            self::$cpuInfo->matchExtra[] = 134;
        }
    }
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(AmigaExecutable::class), "Failed to load class 'Slothsoft\Amber\CLI\AmigaExecutable'!");
    }
    
    protected function setUp(): void {
        parent::setUp();
        TestUtils::changeWorkingDirectoryToComposerRoot();
        self::setUpCpuInfo();
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
        
        $this->assertThat($sut->deplodeInfo, new IsEqual(self::$cpuInfo));
    }
    
    /**
     */
    public function test_deplodeData(): void {
        $in = self::AM2_CPU_DATA_IMPLODED;
        
        $inFile = FileInfoFactory::createFromPath($in);
        $inAccess = new FileDataAccess($inFile, StreamWrapperInterface::MODE_OPEN_READONLY);
        
        $outFile = FileInfoFactory::createTempFile();
        $outAccess = new FileDataAccess($outFile, StreamWrapperInterface::MODE_CREATE_READWRITE);
        
        try {
            AmigaExecutable::deplodeData($inAccess, $outAccess, self::$cpuInfo);
        } catch (Throwable $e) {
            trigger_error((string) $e, E_USER_WARNING);
        }
        
        $this->assertThat($outFile, new FileEqualsFile(self::AM2_CPU_DATA_DEPLODED));
    }
    
    /**
     *
     * @depends test_deplodeData
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