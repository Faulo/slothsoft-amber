<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Savegame\Converter;

final class StringDataAccess implements DataAccessInterface {
    
    public string $data;
    
    private int $position = 0;
    
    private Converter $converter;
    
    public function __construct(string $data) {
        $this->data = $data;
        $this->converter = Converter::getInstance();
    }
    
    public function getPosition(): int {
        return $this->position;
    }
    
    public function setPosition(int $position): void {
        $this->position = $position;
    }
    
    public function readString(int $size, bool $peek = false): string {
        $result = $size === 1 ? $this->data[$this->position] : substr($this->data, $this->position, $size);
        if (! $peek) {
            $this->position += $size;
        }
        return $result;
    }
    
    public function readInteger(int $size, bool $peek = false): int {
        return $this->converter->decodeInteger($this->readString($size, $peek), $size);
    }
    
    public function writeString(string $value): void {
        $this->data .= $value;
        $this->position += strlen($value);
    }
    
    public function writeInteger(int $value, int $size): void {
        $this->data .= $this->converter->encodeInteger($value, $size);
        $this->position += $size;
    }
}