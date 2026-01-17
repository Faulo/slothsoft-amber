<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use BadMethodCallException;

final class HunkDataAccess implements DataAccessInterface {
    
    private Hunk $hunk;
    
    private int $position = 0;
    
    public function __construct(Hunk $hunk) {
        $this->hunk = $hunk;
    }
    
    public function getPosition(): int {
        return $this->position;
    }
    
    public function setPosition(int $position): void {
        $this->position = $position;
    }
    
    public function readString(int $size, bool $peek = false): string {
        $result = $this->hunk->getDataString($this->position, $size);
        if (! $peek) {
            $this->position += $size;
        }
        return $result;
    }
    
    public function readInteger(int $size, bool $peek = false): int {
        $result = $this->hunk->getDataInteger($this->position, $size);
        if (! $peek) {
            $this->position += $size;
        }
        return $result;
    }
    
    public function writeString(string $value): void {
        throw new BadMethodCallException();
    }
    
    public function writeInteger(int $value, int $size): void {
        throw new BadMethodCallException();
    }
}