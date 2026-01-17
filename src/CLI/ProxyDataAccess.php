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
    
    private int $storedPosition;
    
    private function preparePosition(): void {
        $this->storedPosition = $this->data->getPosition();
        $this->data->setPosition($this->position);
    }
    
    private function restorePosition(): void {
        $this->data->setPosition($this->storedPosition);
    }
    
    public function setPosition(int $position): void {
        $this->position = $position;
    }
    
    public function readString(int $size, bool $peek = false): string {
        $this->preparePosition();
        
        if (! $peek and $this->inReverse) {
            $this->position -= $size;
        }
        
        $result = $this->data->readString($size);
        
        if (! $peek and ! $this->inReverse) {
            $this->position += $size;
        }
        
        $this->restorePosition();
        return $result;
    }
    
    public function readInteger(int $size, bool $peek = false): int {
        $this->preparePosition();
        
        if (! $peek and $this->inReverse) {
            $this->position -= $size;
        }
        
        $result = $this->data->readInteger($size);
        
        if (! $peek and ! $this->inReverse) {
            $this->position += $size;
        }
        
        $this->restorePosition();
        return $result;
    }
    
    public function writeString(string $value): void {
        throw new BadMethodCallException();
    }
    
    public function writeInteger(int $value, int $size): void {
        throw new BadMethodCallException();
    }
}