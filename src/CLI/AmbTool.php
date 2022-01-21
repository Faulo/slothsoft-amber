<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use DomainException;
use InvalidArgumentException;
use SplFileInfo;

class AmbTool
{

    const TYPE_RAW = 'Raw';

    const TYPE_AM2 = 'AM2';

    const TYPE_AMBR = 'AMBR';

    const TYPE_JH = 'JH';

    private $ambtoolPath;

    public function __construct(string $ambtoolPath)
    {
        assert(file_exists($ambtoolPath), "ambtool not found at $ambtoolPath");
        
        $this->ambtoolPath = $ambtoolPath;
    }

    private function exec(...$args): array
    {
        $command = escapeshellarg($this->ambtoolPath);
        foreach ($args as $arg) {
            $command .= ' ' . escapeshellarg((string) $arg);
        }
        exec($command, $output);
        return $output;
    }
    public function inspectArchive(SplFileInfo $archivePath): string
    {
        static $inspectCache = [];
        $ret = '';
        if ($archivePath = $archivePath->getRealPath()) {
            if (isset($inspectCache[$archivePath])) {
                $ret = $inspectCache[$archivePath];
            } else {
                $output = $this->exec($archivePath);
                if (isset($output[1])) {
                    $ret = $this->translateAmbtoolFormat($output[1]);
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
        if (! $targetDir->isDir()) {
            mkdir((string) $targetDir, 0777, true);
        }
        $this->exec($archivePath->getRealPath(), $targetDir->getRealPath());
    }
    
    private function translateAmbtoolFormat(string $output) : string {
        switch ($output) {
            case 'Format: JH (encrypted)':                      return self::TYPE_JH;
            case 'Format: AMBR (raw archive)':                  return self::TYPE_AMBR;
            case 'Format: AMNP (compressed/encrypted archive)': return self::TYPE_AMBR;
            case 'Format: AMNC (encrypted archive)':            return self::TYPE_AMBR;
            case 'Format: LOB (compressed)':                    return self::TYPE_AMBR;
            default:
                throw new DomainException("Unknown ambtool.exe format '$output'!");
        }
    }
}

