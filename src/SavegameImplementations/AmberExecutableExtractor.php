<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\SavegameImplementations;

use Slothsoft\Amber\CLI\AmigaExecutable;
use Slothsoft\Amber\CLI\Hunk;
use Slothsoft\Core\FileSystem;
use Slothsoft\Savegame\Node\ArchiveParser\ArchiveExtractorInterface;
use SplFileInfo;

final class AmberExecutableExtractor implements ArchiveExtractorInterface {
    
    public function extractArchive(SplFileInfo $archivePath, SplFileInfo $targetDirectory): bool {
        $executable = new AmigaExecutable();
        $executable->load($archivePath);
        
        $directory = (string) $targetDirectory;
        FileSystem::ensureDirectory($directory);
        
        $codeIndex = 1;
        $dataIndex = 1;
        /** @var Hunk $hunk */
        foreach ($executable->getRealHunks() as $hunk) {
            switch ($hunk->type) {
                case Hunk::TYPE_CODE:
                    $file = $directory . DIRECTORY_SEPARATOR . 'CODE-' . $codeIndex ++;
                    if (! file_put_contents($file, $hunk->data)) {
                        return false;
                    }
                    break;
                case Hunk::TYPE_DATA:
                    $file = $directory . DIRECTORY_SEPARATOR . 'DATA-' . $dataIndex ++;
                    if (! file_put_contents($file, $hunk->data)) {
                        return false;
                    }
                    break;
            }
        }
        
        return true;
    }
}

