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
            $command .= sprintf(' -%s %s', $key, escapeshellarg($val));
        }
        // echo $command . PHP_EOL;
        exec($command, $output);
        return $output;
    }
}

