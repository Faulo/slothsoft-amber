<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use DomainException;
use InvalidArgumentException;
use RuntimeException;
use SplFileInfo;

final class AmbTool {
    
    public static function isSupported(): bool {
        return PHP_OS_FAMILY === 'Windows' or FileSystem::commandExists('wine');
    }
    
    public const TYPE_RAW = 'Raw';
    
    public const TYPE_AM2 = 'AM2';
    
    public const TYPE_AMBR = 'AMBR';
    
    public const TYPE_JH = 'JH';
    
    private string $ambtoolPath;
    
    public function __construct(string $ambtoolPath) {
        assert(file_exists($ambtoolPath), "ambtool not found at $ambtoolPath");
        
        $this->ambtoolPath = $ambtoolPath;
    }
    
    private function exec(string ...$args): string {
        $process = WindowsProcess::create([
            $this->ambtoolPath,
            ...$args
        ]);
        
        $process->run();
        
        if ($process->getExitCode() !== 0) {
            throw new RuntimeException("ambtool failed!" . PHP_EOL . '> ' . $process->getCommandLine() . PHP_EOL . $process->getErrorOutput() . PHP_EOL . $process->getOutput());
        }
        
        return $process->getOutput();
    }
    
    public function inspectArchive(SplFileInfo $archivePath): string {
        static $inspectCache = [];
        $ret = '';
        if ($archivePath = $archivePath->getRealPath()) {
            if (isset($inspectCache[$archivePath])) {
                $ret = $inspectCache[$archivePath];
            } else {
                $output = $this->exec($archivePath);
                $match = [];
                if (preg_match('~Format: .+?\)~', $output, $match)) {
                    $ret = $this->translateAmbtoolFormat($match[0], $archivePath);
                }
                $inspectCache[$archivePath] = $ret;
            }
        }
        return $ret;
    }
    
    public function extractArchive(SplFileInfo $archivePath, SplFileInfo $targetDir): void {
        if (! $archivePath->isFile()) {
            throw new InvalidArgumentException("'$archivePath' is not a valid archive file.");
        }
        
        FileSystem::ensureDirectory((string) $targetDir);
        
        $this->exec($archivePath->getRealPath(), $targetDir->getRealPath());
    }
    
    private function translateAmbtoolFormat(string $output, string $file): string {
        switch ($output) {
            case 'Format: JH (encrypted)':
                return self::TYPE_JH;
            case 'Format: AMBR (raw archive)':
                return self::TYPE_AMBR;
            case 'Format: AMNP (compressed/encrypted archive)':
                return self::TYPE_AMBR;
            case 'Format: AMNC (encrypted archive)':
                return self::TYPE_AMBR;
            case 'Format: LOB (compressed)':
                return self::TYPE_AMBR;
            case 'Format: AMPC (compressed archive)':
                return self::TYPE_AMBR;
            default:
                throw new DomainException("Unknown ambtool.exe format '$output' in  '$file'!");
        }
    }
}

