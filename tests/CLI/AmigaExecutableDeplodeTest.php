<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use Slothsoft\FarahTesting\TestUtils;
use Slothsoft\FarahTesting\Constraints\FileEqualsFile;
use Throwable;

/**
 * AmigaExecutableTest
 *
 * @see AmigaExecutable
 */
final class AmigaExecutableDeplodeTest extends TestCase {
    
    protected function setUp(): void {
        parent::setUp();
        TestUtils::changeWorkingDirectoryToComposerRoot();
    }
    
    /**
     */
    public function test_deplodeData(): void {
        $in = AmigaExecutableTest::AM2_CPU_DATA_IMPLODED;
        $out = temp_file(__CLASS__);
        $accessMode = 'string';
        
        $inAccess = AmigaExecutableTest::createFileReader($in, $accessMode);
        
        $outAccess = AmigaExecutableTest::openFileWriter($out, $accessMode);
        
        try {
            AmigaExecutable::deplodeData($inAccess, $outAccess, AmigaExecutableTest::getCpuInfo());
        } catch (Throwable $e) {
            trigger_error((string) $e, E_USER_WARNING);
        }
        
        AmigaExecutableTest::closeFileWriter($out, $accessMode, $outAccess);
        
        $this->assertThat($out, new FileEqualsFile(AmigaExecutableTest::AM2_CPU_DATA_DEPLODED));
    }
}