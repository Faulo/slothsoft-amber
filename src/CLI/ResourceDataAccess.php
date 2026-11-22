<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

use RuntimeException;
use Slothsoft\Savegame\Converter;

class ResourceDataAccess implements DataAccessInterface {
    
    private Converter $converter;
    
    /** @var resource */
    protected $resource;
    
    /** @param resource $resource */
    public function __construct($resource) {
        $this->resource = $resource;
        $this->converter = Converter::getInstance();
    }
    
    public function getPosition(): int {
        $position = ftell($this->resource);
        if ($position === false) {
            throw new RuntimeException('Could not determine stream position.');
        }
        return $position;
    }
    
    public function setPosition(int $position): void {
        fseek($this->resource, $position, SEEK_SET);
    }
    
    public function readString(int $size, bool $peek = false): string {
        if ($size === 0) {
            return '';
        }
        if ($size < 0) {
            throw new \ValueError('The read size must be greater than or equal to zero.');
        }
        $position = $this->getPosition();
        $result = fread($this->resource, $size);
        if ($peek) {
            $this->setPosition($position);
        }
        if ($result === false) {
            throw new RuntimeException('Could not read from stream.');
        }
        return $result;
    }
    
    public function readInteger(int $size, bool $peek = false): int {
        return $this->converter->decodeInteger($this->readString($size, $peek), $size);
    }
    
    public function writeString(string $value): void {
        fwrite($this->resource, $value);
    }
    
    public function writeInteger(int $value, int $size): void {
        fwrite($this->resource, $this->converter->encodeInteger($value, $size), max(0, $size));
    }
}
