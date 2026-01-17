<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

final class AmigaExecutableDeplodeInfo {
    
    public array $matchBase = [];
    
    public array $matchExtra = [];
    
    public int $implodedSize;
    
    public int $firstLiteralLength;
    
    public int $initialBitBuffer;
}