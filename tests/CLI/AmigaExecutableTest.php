<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\Count;
use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Memory;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use Slothsoft\FarahTesting\TestUtils;
use Slothsoft\FarahTesting\Constraints\FileEqualsFile;
use InvalidArgumentException;
use Throwable;

/**
 * AmigaExecutableTest
 *
 * @see AmigaExecutable
 */
final class AmigaExecutableTest extends TestCase {
    
    private const AM2_CPU_IMPLODED = 'test-files/Amberfiles/AM2_CPU.imploded';
    
    public const AM2_CPU_DATA_IMPLODED = 'test-files/Amberfiles/AM2_CPU_DATA.imploded';
    
    private const AM2_CPU_DEPLODED = 'test-files/Amberfiles/AM2_CPU.deploded';
    
    public const AM2_CPU_DATA_DEPLODED = 'test-files/Amberfiles/AM2_CPU_DATA.deploded';
    
    private const AM2_BLIT_IMPLODED = 'test-files/Amberfiles/AM2_BLIT.imploded';
    
    private const AM2_BLIT_DEPLODED = 'test-files/Amberfiles/AM2_BLIT.deploded';
    
    private static AmigaExecutableDeplodeInfo $cpuInfo;
    
    public static function getCpuInfo(): AmigaExecutableDeplodeInfo {
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
        
        return self::$cpuInfo;
    }
    
    private static array $cpuHunkSizes = [
        183136,
        84728,
        5492,
        15716,
        87856,
        59360
    ];
    
    private static array $cpuMemFlags = [
        0,
        1073741824,
        0,
        1073741824,
        0,
        0
    ];
    
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
        $in = self::AM2_CPU_IMPLODED;
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        try {
            $sut->deplode(false, false);
        } catch (Throwable $e) {}
        
        $this->assertThat($sut->deplodedHunkSizes, new IsEqual(self::$cpuHunkSizes));
    }
    
    /**
     *
     * @depends test_save_deploded
     */
    public function test_deplode_memFlags(): void {
        $in = self::AM2_CPU_IMPLODED;
        
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        try {
            $sut->deplode(false, false);
        } catch (Throwable $e) {}
        
        $this->assertThat($sut->deplodedMemFlags, new IsEqual(self::$cpuMemFlags));
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
            $sut->deplode(false, false);
        } catch (Throwable $e) {}
        
        $this->assertThat($sut->deplodeInfo, new IsEqual(self::getCpuInfo()));
    }
    
    /**
     */
    public function test_load_deplodedHunks(): void {
        $in = self::AM2_CPU_DEPLODED;
        $inFile = FileInfoFactory::createFromPath($in);
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        $this->assertThat([
            ...$sut->getRealHunks()
        ], new Count(13));
    }
    
    /**
     *
     * @dataProvider accessModeProvider
     */
    public function test_createDeplodedHunks(string $accessMode): void {
        $in = self::AM2_CPU_DATA_DEPLODED;
        
        $inAccess = self::createFileReader($in, $accessMode);
        
        try {
            $actual = AmigaExecutable::createDeplodedHunks($inAccess, filesize($in), self::$cpuHunkSizes, self::$cpuMemFlags);
            
            $this->assertThat($actual, new Count(13));
        } catch (Throwable $e) {
            trigger_error((string) $e, E_USER_WARNING);
        }
    }
    
    /**
     *
     * @dataProvider accessModeProvider
     * @depends test_createDeplodedHunks
     */
    public function test_deplodeData(string $accessMode): void {
        $in = self::AM2_CPU_DATA_IMPLODED;
        $out = temp_file(__CLASS__);
        
        $inAccess = self::createFileReader($in, $accessMode);
        
        $outAccess = self::openFileWriter($out, $accessMode);
        
        try {
            AmigaExecutable::deplodeData($inAccess, $outAccess, self::getCpuInfo());
        } catch (Throwable $e) {
            trigger_error((string) $e, E_USER_WARNING);
        }
        
        self::closeFileWriter($out, $accessMode, $outAccess);
        
        $this->assertThat($out, new FileEqualsFile(self::AM2_CPU_DATA_DEPLODED));
    }
    
    public static function createFileReader(string $path, string $mode): DataAccessInterface {
        switch ($mode) {
            case 'string':
                return new StringDataAccess(file_get_contents($path));
            case 'file':
                $file = FileInfoFactory::createFromPath($path);
                return new FileDataAccess($file, StreamWrapperInterface::MODE_OPEN_READONLY);
            case 'tmpfile':
                $resource = tmpfile();
                fwrite($resource, file_get_contents($path));
                rewind($resource);
                return new ResourceDataAccess($resource);
            case 'temp':
                $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
                fwrite($resource, file_get_contents($path));
                rewind($resource);
                return new ResourceDataAccess($resource);
            case 'memory':
                $resource = fopen('php://memory', StreamWrapperInterface::MODE_CREATE_READWRITE);
                fwrite($resource, file_get_contents($path));
                rewind($resource);
                return new ResourceDataAccess($resource);
            default:
                throw new InvalidArgumentException($mode);
        }
    }
    
    public static function openFileWriter(string $path, string $mode): DataAccessInterface {
        switch ($mode) {
            case 'string':
                return new StringDataAccess('');
            case 'file':
                $file = FileInfoFactory::createFromPath($path);
                return new FileDataAccess($file, StreamWrapperInterface::MODE_CREATE_READWRITE);
            case 'tmpfile':
                $resource = tmpfile();
                return new ResourceDataAccess($resource);
            case 'temp':
                $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
                return new ResourceDataAccess($resource);
            case 'memory':
                $resource = fopen('php://memory', StreamWrapperInterface::MODE_CREATE_READWRITE);
                return new ResourceDataAccess($resource);
            default:
                throw new InvalidArgumentException($mode);
        }
    }
    
    public static function closeFileWriter(string $path, string $mode, DataAccessInterface $access): void {
        switch ($mode) {
            case 'string':
                file_put_contents($path, $access->data);
                return;
            case 'file':
                return;
            case 'tmpfile':
            case 'temp':
            case 'memory':
                $access->setPosition(0);
                file_put_contents($path, $access->readString(Memory::ONE_MEGABYTE));
                return;
            default:
                throw new InvalidArgumentException($mode);
        }
    }
    
    public function accessModeProvider(): iterable {
        yield 'string' => [
            'string'
        ];
        
        yield 'file' => [
            'file'
        ];
        
        yield 'tmpfile' => [
            'tmpfile'
        ];
        yield 'php://temp' => [
            'temp'
        ];
        
        yield 'php://memory' => [
            'memory'
        ];
    }
    
    /**
     *
     * @dataProvider fileProvider
     */
    public function test_deplode(string $in, string $out, int $hunkCount): void {
        $inFile = FileInfoFactory::createFromPath($in);
        $actualFile = FileInfoFactory::createTempFile();
        
        $sut = new AmigaExecutable();
        $sut->load($inFile);
        
        $sut->deplode();
        
        $sut->save($actualFile);
        
        $this->assertThat($actualFile, new FileEqualsFile($out));
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