<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Savegame\Converter;
use SplFileInfo;
use SplFileObject;

final class Deploder {
    
    private const SIZEOF_UINT = 4;
    
    private Converter $converter;
    
    private SplFileInfo $inFile;
    
    private SplFileObject $in;
    
    private int $hunkCount;
    
    public function getHunkCount(): int {
        return $this->hunkCount;
    }
    
    public function __construct() {
        $this->converter = Converter::getInstance();
    }
    
    public function load(SplFileInfo $inFile): bool {
        $this->inFile = $inFile;
        $this->in = $inFile->openFile('r');
        
        if ($this->readInt(self::SIZEOF_UINT) !== 0x000003F3) {
            return false;
        }
        if ($this->readInt(self::SIZEOF_UINT) !== 0x00000000) {
            return false;
        }
        
        $this->hunkCount = $this->readInt(self::SIZEOF_UINT);
        
        if ($this->readInt(self::SIZEOF_UINT) !== 0x00000000) {
            return false;
        }
        
        if ($this->readInt(self::SIZEOF_UINT) !== $this->hunkCount - 1) {
            return false;
        }
        
        return true;
    }
    
    private function readInt(int $size = 1): int {
        return $this->converter->decodeInteger($this->in->fread($size), $size);
    }
    
    public static function isSupported(): bool {
        return true;
    }
    
    public function getCompressionMode(SplFileInfo $inFile): int {
        return 0;
    }
    
    public function explode(SplFileInfo $outFile): void {
        $outDirectory = FileInfoFactory::createFromPath($outFile->getPath());
        
        FileSystem::ensureDirectory((string) $outDirectory);
        
        file_put_contents((string) $outFile, '');
    }
}
