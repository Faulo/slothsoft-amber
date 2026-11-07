<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

use BadMethodCallException;

final class ProxyDataAccess implements DataAccessInterface {
    
    private DataAccessInterface $data;
    
    private int $position = 0;
    
    private bool $inReverse;
    
    public function __construct(DataAccessInterface $data, bool $inReverse = false) {
        $this->data = $data;
        $this->inReverse = $inReverse;
    }
    
    public function getPosition(): int {
        return $this->position;
    }
    
    public function setPosition(int $position): void {
        $this->position = $position;
    }
    
    public function readString(int $size, bool $peek = false): string {
        $readPosition = $this->inReverse ? $this->position - $size : $this->position;
        $storedPosition = $this->data->getPosition();
        
        try {
            $this->data->setPosition($readPosition);
            $result = $this->data->readString($size, true);
        } finally {
            $this->data->setPosition($storedPosition);
        }
        
        if (! $peek) {
            $this->position = $this->inReverse ? $readPosition : $this->position + $size;
        }
        
        return $result;
    }
    
    public function readInteger(int $size, bool $peek = false): int {
        $readPosition = $this->inReverse ? $this->position - $size : $this->position;
        $storedPosition = $this->data->getPosition();
        
        try {
            $this->data->setPosition($readPosition);
            $result = $this->data->readInteger($size, true);
        } finally {
            $this->data->setPosition($storedPosition);
        }
        
        if (! $peek) {
            $this->position = $this->inReverse ? $readPosition : $this->position + $size;
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
