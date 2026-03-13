<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\SavegameImplementations;

final class FileStub {
    
    public string $content;
    
    public string $fileName;
    
    public string $archivePath;
    
    public function getContent(): string {
        return $this->content;
    }
    
    public function getFileName(): string {
        return $this->fileName;
    }
    
    public function getArchivePath(): string {
        return $this->archivePath;
    }
    
    public function getParentNode(): self {
        return $this;
    }
}