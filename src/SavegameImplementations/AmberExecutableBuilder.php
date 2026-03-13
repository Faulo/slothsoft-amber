<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\SavegameImplementations;

use Slothsoft\Amber\CLI\AmigaExecutable;
use Slothsoft\Amber\CLI\Hunk;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Savegame\Node\FileContainer;
use Slothsoft\Savegame\Node\ArchiveParser\ArchiveBuilderInterface;
use SplFileInfo;

final class AmberExecutableBuilder implements ArchiveBuilderInterface {
    
    private SplFileInfo $sourceDirectory;
    
    private array $executables = [];
    
    public function __construct(SplFileInfo $sourceDirectory) {
        $this->sourceDirectory = $sourceDirectory;
    }
    
    private function loadExecutable(string $archivePath): AmigaExecutable {
        if (! isset($this->executables[$archivePath])) {
            $executable = new AmigaExecutable();
            $executable->load(FileInfoFactory::createFromPath($this->sourceDirectory . DIRECTORY_SEPARATOR . $archivePath));
            if ($executable->requiresDeploding()) {
                $executable->deplode();
            }
            $this->executables[$archivePath] = $executable;
        }
        
        return $this->executables[$archivePath];
    }
    
    public function buildArchive(iterable $buildChildren): string {
        /** @var ?AmigaExecutable $executable */
        $executable = null;
        
        /** @var FileContainer $child */
        foreach ($buildChildren as $child) {
            if ($executable === null) {
                $path = $child->getParentNode()->getArchivePath();
                $executable = $this->loadExecutable($path);
            }
            
            $fileName = $child->getFileName();
            $codeIndex = 1;
            $dataIndex = 1;
            /** @var Hunk $hunk */
            foreach ($executable->getRealHunks() as $hunk) {
                $hunkName = null;
                switch ($hunk->type) {
                    case Hunk::TYPE_CODE:
                        $hunkName = 'CODE-' . $codeIndex ++;
                        break;
                    case Hunk::TYPE_DATA:
                        $hunkName = 'DATA-' . $dataIndex ++;
                        break;
                }
                
                if ($fileName === $hunkName) {
                    $hunk->data = $child->getContent();
                    break;
                }
            }
        }
        
        if ($executable === null) {
            return '';
        }
        
        $file = FileInfoFactory::createTempFile();
        $executable->save($file);
        
        return file_get_contents((string) $file);
    }
}

