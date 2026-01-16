<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;

final class Deploder {
    
    public static function isSupported(): bool {
        return true;
    }
    
    public function getCompressionMode(SplFileInfo $inFile): int {
        return 0;
    }
    
    public function explode(SplFileInfo $inFile, SplFileInfo $outFile): void {
        $outDirectory = FileInfoFactory::createFromPath($outFile->getPath());
        
        FileSystem::ensureDirectory((string) $outDirectory);
        
        file_put_contents((string) $outFile, '');
    }
}

