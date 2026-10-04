<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

final class AmbGfx {
    
    public static function isSupported(): bool {
        return NativeExecutable::isSupported('amgfx');
    }
    
    private string $ambgfxPath;
    
    public function __construct(string $ambgfxPath) {
        assert(file_exists($ambgfxPath), "ambgfx not found at $ambgfxPath");
        
        $this->ambgfxPath = $ambgfxPath;
    }
    
    private function exec(string $file, array $args): void {
        $command = [];
        
        $command[] = $this->ambgfxPath;
        $command[] = $file;
        
        foreach ($args as $key => $value) {
            $command[] = "-$key";
            $command[] = $value;
        }
        
        NativeExecutable::execute('amgfx', $command);
    }
    
    public function extractTga(SplFileInfo $inFile, SplFileInfo $outFile, int $width = 32, int $bitplanes = 5, int $offset = 0, int $size = 0, int $palette = 49, int $firstColor = 0): void {
        $outDirectory = FileInfoFactory::createFromPath($outFile->getPath());
        
        FileSystem::ensureDirectory((string) $outDirectory);
        
        $options = [
            'out' => (string) $outFile,
            'width' => $width,
            'bpl' => $bitplanes,
            'off' => $offset,
            'pal' => $palette,
            'col' => $firstColor
        ];
        if ($size > 0) {
            $options['size'] = $size;
        }
        
        $this->exec($inFile->getRealPath(), $options);
    }
}
