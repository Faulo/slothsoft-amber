<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Savegame\Converter;

class ResourceDataAccess implements DataAccessInterface {
    
    private Converter $converter;
    
    private $resource;
    
    public function __construct($resource) {
        $this->resource = $resource;
        $this->converter = Converter::getInstance();
    }
    
    public function getPosition(): int {
        return ftell($this->resource);
    }
    
    public function setPosition(int $position): void {
        fseek($this->resource, $position, SEEK_SET);
    }
    
    public function readString(int $size, bool $peek = false): string {
        $result = fread($this->resource, $size);
        if ($peek) {
            fseek($this->resource, - $size, SEEK_CUR);
        }
        return $result;
    }
    
    public function readInteger(int $size, bool $peek = false): int {
        $result = $this->converter->decodeInteger(fread($this->resource, $size), $size);
        if ($peek) {
            fseek($this->resource, - $size, SEEK_CUR);
        }
        return $result;
    }
    
    public function writeString(string $value): void {
        fwrite($this->resource, $value);
    }
    
    public function writeInteger(int $value, int $size): void {
        fwrite($this->resource, $this->converter->encodeInteger($value, $size), $size);
    }
}