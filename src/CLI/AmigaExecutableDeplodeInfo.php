<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

final class AmigaExecutableDeplodeInfo {
    
    /** @var list<int> */
    public array $matchBase = [];
    
    /** @var list<int> */
    public array $matchExtra = [];
    
    public int $implodedSize;
    
    public int $firstLiteralLength;
    
    public int $initialBitBuffer;
}