<?php
namespace Slothsoft\Amber\CLI;

class AmbTool
{
    const TYPE_RAW = 'Raw';
    
    const TYPE_AM2 = 'AM2';
    
    const TYPE_AMBR = 'AMBR';
    
    const TYPE_JH = 'JH';
    
    private $ambtoolPath;
    public function __construct(string $ambtoolPath) {
        assert(file_exists($ambtoolPath), "ambtool not found at $ambtoolPath");
        
        $this->ambtoolPath = $ambtoolPath;
    }
    public function exec(...$args) : array
    {
        $command = escapeshellarg($this->ambtoolPath);
        foreach ($args as $arg) {
            $command .= ' ' . escapeshellarg($arg);
        }
        exec($command, $output);
        return $output;
    }
    public function inspectArchive(string $archivePath): string
    {
        static $inspectCache = [];
        $ret = '';
        if ($archivePath = realpath($archivePath)) {
            if (isset($inspectCache[$archivePath])) {
                $ret = $inspectCache[$archivePath];
            } else {
                $output = $this->exec($archivePath);
                if (isset($output[1])) {
                    $ret = $output[1];
                }
                $inspectCache[$archivePath] = $ret;
            }
        }
        return $ret;
    }
}
