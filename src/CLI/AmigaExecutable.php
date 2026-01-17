<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Ds\Map;
use PHPUnit\Util\InvalidDataSetException;
use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use SplFileInfo;
use UnexpectedValueException;

final class AmigaExecutable {
    
    public static function isSupported(): bool {
        return true;
    }
    
    private const SIZEOF_BYTE = 1;
    
    private const SIZEOF_WORD = 2;
    
    private const SIZEOF_UINT = 4;
    
    private const SIZEOF_DWORD = 4;
    
    private ?DataAccessInterface $in;
    
    private ?DataAccessInterface $out;
    
    private int $realHunkCount;
    
    private int $totalHunkCount;
    
    private array $hunks;
    
    public function getRealHunkCount(): int {
        return $this->realHunkCount;
    }
    
    public function load(SplFileInfo $inFile): void {
        $this->in = new FileDataAccess($inFile, StreamWrapperInterface::MODE_OPEN_READONLY);
        
        if ($this->readInt(self::SIZEOF_UINT) !== 0x000003F3) {
            throw new UnexpectedValueException('0x000003F3');
        }
        if ($this->readInt(self::SIZEOF_UINT) !== 0x00000000) {
            throw new UnexpectedValueException('0x00000000');
        }
        
        $this->realHunkCount = $this->readInt(self::SIZEOF_UINT);
        $this->totalHunkCount = $this->realHunkCount;
        
        if ($this->readInt(self::SIZEOF_UINT) !== 0x00000000) {
            throw new UnexpectedValueException('0x00000000');
        }
        
        if ($this->readInt(self::SIZEOF_UINT) !== $this->totalHunkCount - 1) {
            throw new UnexpectedValueException($this->totalHunkCount - 1);
        }
        
        $this->hunks = [];
        
        $realHunks = [];
        for ($i = 0; $i < $this->realHunkCount; $i ++) {
            $header = $this->readInt(self::SIZEOF_UINT);
            $hunk = new Hunk();
            $hunk->size = $header & 0x3FFFFFFF;
            $hunk->memoryFlags = $header & 0xC0000000;
            if ($hunk->memoryFlags === 0xC0000000) {
                $hunk->memoryFlags = $this->readInt(self::SIZEOF_UINT) & 0x80000000;
                throw new InvalidDataSetException("This implementation can't deal with double-sized headers.");
            }
            $realHunks[] = $hunk;
        }
        
        for ($i = 0, $j = 0; $i < $this->totalHunkCount; $i ++) {
            $type = $this->readInt(self::SIZEOF_UINT) & 0x1fffffff;
            switch ($type) {
                case Hunk::TYPE_CODE:
                case Hunk::TYPE_DATA:
                    /** @var Hunk $hunk */
                    $hunk = $realHunks[$j ++];
                    $hunk->type = $type;
                    $hunk->numEntries = $this->readInt(self::SIZEOF_UINT);
                    $hunk->data = $this->readString($hunk->numEntries * 4);
                    $this->hunks[] = $hunk;
                    break;
                case Hunk::TYPE_BSS:
                    /** @var Hunk $hunk */
                    $hunk = $realHunks[$j ++];
                    $hunk->type = $type;
                    $hunk->numEntries = $this->readInt(self::SIZEOF_UINT);
                    $this->hunks[] = $hunk;
                    break;
                case Hunk::TYPE_RELOC32:
                    $start = $this->in->getPosition();
                    $entries = new Map();
                    
                    while (($offsetCount = $this->readInt(self::SIZEOF_UINT)) !== 0) {
                        $hunkNumber = $this->readInt(self::SIZEOF_UINT);
                        $list = [];
                        for ($o = 0; $o < $offsetCount; $o ++) {
                            $list[] = $this->readInt(self::SIZEOF_UINT);
                        }
                        $entries->put($hunkNumber, $list);
                    }
                    
                    $size = $this->in->getPosition() - $start;
                    
                    $this->hunks[] = Hunk::createReloc32($size, $entries);
                    $this->totalHunkCount ++;
                    break;
                case Hunk::TYPE_END:
                    $this->hunks[] = Hunk::createEnd();
                    $this->totalHunkCount ++;
                    break;
                default:
                    throw new UnexpectedValueException("Dunno what to do with hunk type '$type'");
            }
        }
        
        if ($this->in->getPosition() + self::SIZEOF_UINT === $inFile->getSize()) {
            $type = $this->readInt(self::SIZEOF_UINT) & 0x1fffffff;
            if ($type !== Hunk::TYPE_END) {
                throw new UnexpectedValueException((string) $type);
            }
            $this->hunks[] = Hunk::createEnd();
        }
        
        if ($this->in->getPosition() !== $inFile->getSize()) {
            throw new UnexpectedValueException((string) $this->in->getPosition());
        }
        
        $this->in = null;
    }
    
    private function readString(int $size): string {
        return $this->in->readString($size);
    }
    
    private function readInt(int $size): int {
        return $this->in->readInteger($size);
    }
    
    public function save(SplFileInfo $outFile): void {
        $outDirectory = FileInfoFactory::createFromPath($outFile->getPath());
        
        FileSystem::ensureDirectory((string) $outDirectory);
        
        $this->out = new FileDataAccess($outFile, StreamWrapperInterface::MODE_CREATE_WRITEONLY);
        
        $this->writeInt(0x000003F3, self::SIZEOF_UINT);
        $this->writeInt(0, self::SIZEOF_UINT);
        $this->writeInt($this->realHunkCount, self::SIZEOF_UINT);
        $this->writeInt(0, self::SIZEOF_UINT);
        $this->writeInt($this->realHunkCount - 1, self::SIZEOF_UINT);
        
        /** @var Hunk $hunk */
        foreach ($this->hunks as $hunk) {
            if ($hunk->isReal()) {
                $header = $hunk->size | $hunk->memoryFlags;
                $this->writeInt($header, self::SIZEOF_UINT);
            }
        }
        
        /** @var Hunk $hunk */
        foreach ($this->hunks as $hunk) {
            $this->writeInt($hunk->type, self::SIZEOF_UINT);
            
            switch ($hunk->type) {
                case Hunk::TYPE_CODE:
                case Hunk::TYPE_DATA:
                    $this->writeInt($hunk->numEntries, self::SIZEOF_UINT);
                    $this->writeString($hunk->data);
                    break;
                case Hunk::TYPE_BSS:
                    $this->writeInt($hunk->numEntries, self::SIZEOF_UINT);
                    break;
                case Hunk::TYPE_RELOC32:
                    $start = $this->out->getPosition();
                    /** @var array $entry */
                    foreach ($hunk->entries as $hunkNumber => $entry) {
                        $this->writeInt(count($entry), self::SIZEOF_UINT);
                        $this->writeInt($hunkNumber, self::SIZEOF_UINT);
                        foreach ($entry as $offset) {
                            $this->writeInt($offset, self::SIZEOF_UINT);
                        }
                    }
                    $this->writeInt(0, self::SIZEOF_UINT);
                    $size = $this->out->getPosition() - $start;
                    if ($size !== $hunk->size) {
                        throw new UnexpectedValueException("RELOC32 has size '$hunk->size', but entries are $size bytes long.");
                    }
                    break;
                case Hunk::TYPE_END:
                    break;
                default:
                    throw new UnexpectedValueException("Dunno what to do with hunk type '$hunk->type'");
            }
        }
        
        $this->out = null;
    }
    
    private function writeString(string $value): void {
        $this->out->writeString($value);
    }
    
    private function writeInt(int $value, int $size): void {
        $this->out->writeInteger($value, $size);
    }
    
    public function deplode(bool $loadDeplodedData = true, bool $replaceImplodedHunks = true): void {
        $this->loadDeplodeInfo();
        
        if ($loadDeplodedData) {
            
            $temp = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
            
            $tempAccess = new ResourceDataAccess($temp);
            
            $this->loadDeplodedData($tempAccess);
            
            if ($replaceImplodedHunks) {
                
                $this->replaceImplodedHunks($tempAccess);
            }
            
            fclose($temp);
        }
    }
    
    public array $deplodedHunkSizes;
    
    public array $deplodedMemFlags;
    
    private Hunk $lastDataHunk;
    
    public AmigaExecutableDeplodeInfo $deplodeInfo;
    
    public int $deplodedSize;
    
    /**
     *
     * @link https://github.com/Pyrdacor/Ambermoon.net/blob/master/Ambermoon.Data.Legacy/Serialization/AmigaExecutable.cs
     */
    private function loadDeplodeInfo(): void {
        $lastCodeHunk = null;
        $lastDataHunk = null;
        $this->deplodedHunkSizes = [];
        $this->deplodedMemFlags = [];
        /** @var Hunk $hunk */
        foreach ($this->hunks as $i => $hunk) {
            switch ($hunk->type) {
                case Hunk::TYPE_CODE:
                    $lastCodeHunk = $hunk;
                    break;
                case Hunk::TYPE_DATA:
                    $lastDataHunk = $hunk;
                    break;
                case Hunk::TYPE_BSS:
                    $this->deplodedHunkSizes[] = $hunk->numEntries * 4;
                    $this->deplodedMemFlags[] = $hunk->memoryFlags;
                    break;
            }
        }
        
        if (! $lastCodeHunk or ! $lastDataHunk or ! $this->deplodedHunkSizes or ! $this->deplodedMemFlags) {
            throw new UnexpectedValueException('missing hunks');
        }
        
        array_pop($this->deplodedHunkSizes);
        array_pop($this->deplodedMemFlags);
        
        $this->deplodeInfo = new AmigaExecutableDeplodeInfo();
        
        for ($i = 0; $i < 8; $i ++) {
            $this->deplodeInfo->matchBase[] = $lastCodeHunk->getDataInteger(0x188 + $i * 2, 2);
        }
        
        for ($i = 0; $i < 12; $i ++) {
            $this->deplodeInfo->matchExtra[] = $lastCodeHunk->getDataInteger(0x188 + 16 + $i);
        }
        
        $this->deplodeInfo->firstLiteralLength = $lastCodeHunk->getDataInteger(0x1E6, 2);
        $this->deplodeInfo->initialBitBuffer = $lastCodeHunk->getDataInteger(0x1E8);
        $this->deplodeInfo->implodedSize = $lastCodeHunk->getDataInteger(8, 4);
        
        $this->lastDataHunk = $lastDataHunk;
    }
    
    private function loadDeplodedData(DataAccessInterface $output) {
        $lastDataHunkAccess = new HunkDataAccess($this->lastDataHunk);
        
        self::deplodeData($lastDataHunkAccess, $output, $this->deplodeInfo);
        
        $this->deplodedSize = $output->getPosition();
    }
    
    private static array $DeplodeLiteralBase = [
        6,
        10,
        10,
        18
    ];
    
    private static array $DeplodeLiteralExtraBits = [
        1,
        1,
        1,
        1,
        2,
        3,
        3,
        4,
        4,
        5,
        7,
        14
    ];
    
    public static function deplodeData(DataAccessInterface $input, DataAccessInterface $output, AmigaExecutableDeplodeInfo $info): void {
        $output->setPosition(0);
        $currentOutput = new ProxyDataAccess($output);
        $reverseInput = new ProxyDataAccess($input, true);
        $reverseInput->setPosition($info->implodedSize);
        $literalLength = $info->firstLiteralLength; // word at offset 0x1E6 in the last code hunk
        $bitBuffer = $info->initialBitBuffer; // byte at offset 0x1E8 in the last code hunk
        
        $readBits = function (int $count) use ($reverseInput, &$bitBuffer): int {
            $result = 0;
            
            if (($count & 0x80) !== 0) {
                $result = $reverseInput->readInteger(self::SIZEOF_BYTE);
                $count &= 0x7f;
            }
            
            for ($i = 0; $i < $count; $i ++) {
                $bit = $bitBuffer >> 7;
                $bitBuffer = ($bitBuffer << 1) % 256;
                
                if ($bitBuffer == 0) {
                    $temp = $bit;
                    $bitBuffer = $reverseInput->readInteger(self::SIZEOF_BYTE);
                    $bit = $bitBuffer >> 7;
                    $bitBuffer = ($bitBuffer << 1) % 256;
                    
                    if ($temp !== 0) {
                        $bitBuffer ++;
                    }
                }
                
                $result <<= 1;
                $result |= $bit;
            }
            
            return $result;
        };
        
        while ($reverseInput->getPosition() > 0) {
            if ($literalLength > 0) {
                $output->writeString(strrev($reverseInput->readString($literalLength)));
            }
            
            if ($reverseInput->getPosition() <= 0) {
                break;
            }
            
            /*
             * static Huffman encoding of the match length and selector:
             *
             * 0 -> selector = 0, match_len = 1
             * 10 -> selector = 1, match_len = 2
             * 110 -> selector = 2, match_len = 3
             * 1110 -> selector = 3, match_len = 4
             * 11110 -> selector = 3, match_len = 5 + next three bits (5-12)
             * 11111 -> selector = 3, match_len = (next input byte)-1 (0-254)
             *
             */
            $selector = 0;
            $matchLength = 0;
            if ($readBits(1) !== 0) {
                if ($readBits(1) !== 0) {
                    if ($readBits(1) !== 0) {
                        $selector = 3;
                        
                        if ($readBits(1) !== 0) {
                            if ($readBits(1) !== 0) { // 11111
                                $matchLength = $reverseInput->readInteger(self::SIZEOF_BYTE);
                            } else { // 11110
                                $matchLength = 6 + $readBits(3);
                            }
                        } else { // 1110
                            $matchLength = 5;
                        }
                    } else { // 110
                        $selector = 2;
                        $matchLength = 4;
                    }
                } else { // 10
                    $selector = 1;
                    $matchLength = 3;
                }
            } else { // 0
                $selector = 0;
                $matchLength = 2;
            }
            
            if ($matchLength <= 0) {
                throw new UnexpectedValueException('matchLength');
            }
            
            /*
             * another Huffman tuple, for deciding the base value (y) and number
             * of extra bits required from the input stream (x) to create the
             * length of the next literal run. Selector is 0-3, as previously
             * obtained.
             *
             * 0 -> base = 0, extra = {1,1,1,1}[selector]
             * 10 -> base = 2, extra = {2,3,3,4}[selector]
             * 11 -> base = {6,10,10,18}[selector] extra = {4,5,7,14}[selector]
             */
            $y = 0;
            $x = $selector;
            if ($readBits(1) !== 0) {
                if ($readBits(1) !== 0) { // 11
                    $y = self::$DeplodeLiteralBase[$x];
                    $x += 8;
                } else { // 10
                    $y = 2;
                    $x += 4;
                }
            }
            $x = self::$DeplodeLiteralExtraBits[$x];
            
            /* next literal run length: read [x] bits and add [y] */
            $literalLength = $y + $readBits($x);
            
            /*
             * another Huffman tuple, for deciding the match distance: _base and
             * _extra are from the explosion table, as passed into the deplode
             * function.
             *
             * 0 -> base = 1 extra = _extra[selector + 0]
             * 10 -> base = 1 + _base[selector + 0] extra = _extra[selector + 4]
             * 11 -> base = 1 + _base[selector + 4] extra = _extra[selector + 8]
             */
            $match = $output->getPosition() - 1;
            $x = $selector;
            if ($readBits(1) !== 0) {
                if ($readBits(1) !== 0) {
                    $match -= $info->matchBase[$selector + 4];
                    $x += 8;
                } else {
                    $match -= $info->matchBase[$selector];
                    $x += 4;
                }
            }
            $x = $info->matchExtra[$x];
            
            /*
             * obtain the value of the next [x] extra bits and
             * add it to the match offset
             */
            $match -= $readBits($x);
            
            $currentOutput->setPosition($match);
            
            $availableLength = $output->getPosition() - $match;
            
            /* copy match */
            while ($matchLength > 0) {
                // match may include output yet to be hidden, beware
                $deltaLength = min($matchLength, $availableLength);
                $output->writeString($currentOutput->readString($deltaLength));
                $matchLength -= $deltaLength;
            }
        }
    }
    
    private function replaceImplodedHunks(DataAccessInterface $deploded): void {
        $deplodedSize = $this->deplodedSize;
        $deploded->setPosition(0);
        
        $readString = function (int $size) use ($deploded): string {
            $result = $deploded->readString($size);
            $resultSize = strlen($result);
            if ($resultSize !== $size) {
                $missing = $size - $resultSize;
                throw new UnexpectedValueException("Tried to read $size bytes, but only found $resultSize. Missing $missing bytes.");
            }
            return $result;
        };
        $readInt = function (int $size = self::SIZEOF_UINT) use ($deploded): int {
            return $deploded->readInteger($size);
        };
        $peekInt = function (int $size = self::SIZEOF_UINT) use ($deploded): int {
            return $deploded->readInteger($size, true);
        };
        $eof = function (int $sizeLeft = 0) use ($deploded, $deplodedSize): bool {
            return $deploded->getPosition() + $sizeLeft >= $deplodedSize;
        };
        
        $hunks = [];
        
        /** @var Hunk $hunk */
        $hunk = null;
        
        $hunkSizeIndex = 0;
        
        while (! $eof()) {
            $header = $readInt();
            $flags = $header >> 30;
            $hunkSize = $header & 0x3FFFFFFF;
            
            // Note: The following is just guessing from analyzing the data but it works quite good.
            // Code hunks seem to have flags = 0.
            // BSS and DATA have flags = 2 or 3 (BSS has size 0).
            // 3 is used if no END hunk follows. This is the case for DATA hunks with RELOC32 following or hunks at the end.
            // RELOC32 seems to have flags = 1.
            // END hunks are inserted after each hunk expect for flags = 3 or if a RELOC32 follows.
            // An END hunk should also not be added at the very end.
            
            switch ($flags) {
                case 0:
                    // Code
                    if ($hunkSize * 4 !== $this->deplodedHunkSizes[$hunkSizeIndex]) {
                        $hunkSize *= 4;
                        throw new UnexpectedValueException("Invalid hunk data size '$hunkSize', expected '{$this->deplodedHunkSizes[$hunkSizeIndex]}'.");
                    }
                    $hunks[] = Hunk::createCode($this->deplodedMemFlags[$hunkSizeIndex], $hunkSize, $readString($this->deplodedHunkSizes[$hunkSizeIndex]));
                    $hunkSizeIndex ++;
                    break;
                case 2:
                case 3:
                    $isBSS = ($eof(self::SIZEOF_UINT) or ($peekInt() & 0x3fffffff === 0)); // a size follows -> no BSS but DATA
                    if ($isBSS) {
                        // BSS
                        if ($hunkSizeIndex == count($this->deplodedHunkSizes)) {
                            throw new UnexpectedValueException("Invalid hunk size index '$hunkSizeIndex'.");
                        }
                        
                        $hunks[] = Hunk::createBSS($this->deplodedMemFlags[$hunkSizeIndex], $this->deplodedHunkSizes[$hunkSizeIndex] / 4);
                    } else {
                        // Data
                        $hunkSize = $readInt() & 0x3fffffff;
                        
                        if ($hunkSize * 4 !== $this->deplodedHunkSizes[$hunkSizeIndex]) {
                            throw new UnexpectedValueException("Invalid hunk data size '$hunkSize'.");
                        }
                        
                        $hunks[] = Hunk::createData($this->deplodedMemFlags[$hunkSizeIndex], $hunkSize, $readString($hunkSize * 4));
                    }
                    
                    $hunkSizeIndex ++;
                    break;
                default:
                    throw new UnexpectedValueException("Invalid hunk flag '$flags'.");
            }
            
            if (! $eof(self::SIZEOF_UINT)) {
                $nextHeader = $peekInt();
                $nextFlags = $nextHeader >> 30;
                if ($nextFlags == 1) { // RELOC32 follows, do not add END
                    continue;
                }
            }
            
            switch ($flags) {
                case 3:
                    break;
                default:
                    $hunks[] = Hunk::createEnd();
                    break;
            }
        }
        
        $hunk = array_pop($hunks);
        if ($hunk->type !== Hunk::TYPE_END) {
            $hunks[] = $hunk;
        }
        
        $this->hunks = $hunks;
    }
}
