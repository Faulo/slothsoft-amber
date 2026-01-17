<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Savegame\Converter;
use SplFileInfo;
use SplFileObject;

final class FileDataAccess implements DataAccessInterface {
    
    private Converter $converter;
    
    private SplFileObject $file;
    
    public function __construct(SplFileInfo $file, string $mode) {
        $this->file = $file->openFile($mode);
        $this->converter = Converter::getInstance();
    }
    
    public function getPosition(): int {
        return $this->file->ftell();
    }
    
    public function setPosition(int $position): void {
        $this->file->fseek($position, SEEK_SET);
    }
    
    public function readString(int $size, bool $peek = false): string {
        $result = $this->file->fread($size);
        if ($peek) {
            $this->file->fseek(- $size, SEEK_CUR);
        }
        return $result;
    }
    
    public function readInteger(int $size, bool $peek = false): int {
        $result = $this->converter->decodeInteger($this->readString($size), $size);
        if ($peek) {
            $this->file->fseek(- $size, SEEK_CUR);
        }
        return $result;
    }
    
    public function writeString(string $value): void {
        $this->file->fwrite($value);
    }
    
    public function writeInteger(int $value, int $size): void {
        $this->file->fwrite($this->converter->encodeInteger($value, $size), $size);
    }
}