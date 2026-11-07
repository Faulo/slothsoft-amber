<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

interface DataAccessInterface {
    
    public function getPosition(): int;
    
    public function setPosition(int $position): void;
    
    /** @phpstan-impure */
    public function readString(int $size, bool $peek = false): string;
    
    /** @phpstan-impure */
    public function readInteger(int $size, bool $peek = false): int;
    
    public function writeString(string $value): void;
    
    public function writeInteger(int $value, int $size): void;
}
