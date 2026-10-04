<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * NativeExecutableTest
 *
 * @see NativeExecutable
 */
final class NativeExecutableTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(NativeExecutable::class), "Failed to load class 'Slothsoft\Amber\CLI\NativeExecutable'!");
    }
    
    public function testExecuteReturnsOutputAndPreservesArguments(): void {
        $script = $this->createScript(<<<'PHP'
fwrite(STDOUT, $argv[1]);
PHP
        );
        $argument = 'spaces & shell | characters < "quotes"';
        
        $output = NativeExecutable::execute('test process', [
            PHP_BINARY,
            $script,
            $argument
        ]);
        
        $this->assertSame($argument, $output);
    }
    
    public function testExecuteReportsExitCodeAndCapturedOutputOnFailure(): void {
        $script = $this->createScript(<<<'PHP'
fwrite(STDOUT, 'standard output');
fwrite(STDERR, 'error output');
exit(23);
PHP
        );
        
        try {
            NativeExecutable::execute('test process', [
                PHP_BINARY,
                $script
            ]);
            $this->fail('Expected the failed process to throw an exception.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('test process failed with exit code 23!', $e->getMessage());
            $this->assertStringContainsString('standard output', $e->getMessage());
            $this->assertStringContainsString('error output', $e->getMessage());
        }
    }
    
    private function createScript(string $code): string {
        $path = temp_file(__CLASS__, 'native-process-');
        file_put_contents($path, "<?php\n" . $code);
        return $path;
    }
}