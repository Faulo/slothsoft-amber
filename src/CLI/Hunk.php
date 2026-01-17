<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Ds\Map;
use Slothsoft\Savegame\Converter;

final class Hunk {
    
    // 0x3E9
    public const TYPE_CODE = 1001;
    
    // 0x3EA
    public const TYPE_DATA = 1002;
    
    // 0x3EB
    public const TYPE_BSS = 1003;
    
    // 0x3EC
    public const TYPE_RELOC32 = 1004;
    
    // 0x3F2
    public const TYPE_END = 1010;
    
    public static function createCode(int $memoryFlags, int $numEntries, string $data): Hunk {
        $hunk = new Hunk();
        $hunk->type = self::TYPE_CODE;
        $hunk->memoryFlags = $memoryFlags;
        $hunk->size = $numEntries;
        $hunk->numEntries = $numEntries;
        $hunk->data = $data;
        return $hunk;
    }
    
    public static function createData(int $memoryFlags, int $numEntries, string $data): Hunk {
        $hunk = new Hunk();
        $hunk->type = self::TYPE_DATA;
        $hunk->memoryFlags = $memoryFlags;
        $hunk->size = $numEntries;
        $hunk->numEntries = $numEntries;
        $hunk->data = $data;
        return $hunk;
    }
    
    public static function createBSS(int $memoryFlags, int $numEntries): Hunk {
        $hunk = new Hunk();
        $hunk->type = self::TYPE_BSS;
        $hunk->memoryFlags = $memoryFlags;
        $hunk->size = $numEntries;
        $hunk->numEntries = $numEntries;
        return $hunk;
    }
    
    public static function createReloc32(int $size, Map $entries, int $memoryFlags = 0): Hunk {
        $hunk = new Hunk();
        $hunk->type = self::TYPE_RELOC32;
        $hunk->size = $size;
        $hunk->entries = $entries;
        $hunk->memoryFlags = $memoryFlags;
        return $hunk;
    }
    
    public static function createEnd(): Hunk {
        $hunk = new Hunk();
        $hunk->type = self::TYPE_END;
        return $hunk;
    }
    
    public int $type;
    
    public function getTypeName(): string {
        switch ($this->type) {
            case Hunk::TYPE_CODE:
                return 'Code';
            case Hunk::TYPE_DATA:
                return 'Data';
            case Hunk::TYPE_BSS:
                return 'BSS';
            case Hunk::TYPE_RELOC32:
                return 'RELOC32';
            case Hunk::TYPE_END:
                return 'End';
            default:
                return 'Unknown';
        }
    }
    
    public int $memoryFlags;
    
    public int $size;
    
    public ?string $data;
    
    public int $numEntries;
    
    public ?Map $entries;
    
    public function isReal(): bool {
        switch ($this->type) {
            case Hunk::TYPE_CODE:
                return true;
            case Hunk::TYPE_DATA:
                return true;
            case Hunk::TYPE_BSS:
                return true;
            default:
                return false;
        }
    }
    
    public function getDataString(int $offset, int $size = 1): string {
        return substr($this->data, $offset, $size);
    }
    
    public function getDataInteger(int $offset, int $size = 1): int {
        return Converter::getInstance()->decodeInteger($this->getDataString($offset, $size), $size);
    }
}