<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

class AmbGfx
{

    private $ambgfxPath;

    public function __construct(string $ambgfxPath)
    {
        assert(file_exists($ambgfxPath), "ambgfx not found at $ambgfxPath");
        
        $this->ambgfxPath = $ambgfxPath;
    }

    public function exec(string $file, array $args)
    {
        $command = escapeshellarg($this->ambgfxPath) . ' ' . escapeshellarg($file);
        foreach ($args as $key => $val) {
            $command .= sprintf(' -%s %s', $key, escapeshellarg((string) $val));
        }
        echo $command . PHP_EOL;
        exec($command, $output);
        return $output;
    }
    
    public function extractTga(string $inFile, string $outFile, int $width = 16, int $bitplanes = 5, int $palette = 0, int $firstColor = 0) {
        return $this->exec(
            $inFile,
            [
                'out' => $outFile,
                'w' => $width,
                'b' => $bitplanes,
                'p' => $palette,
                'c' => $firstColor,
            ]
        );
    }
}

