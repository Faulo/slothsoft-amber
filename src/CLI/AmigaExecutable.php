<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

use Ds\Map;
use Generator;
use RuntimeException;
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
    
    private const SIZEOF_UINT = 4;
    
    private int $realHunkCount;
    
    private int $totalHunkCount;
    
    /** @var list<Hunk> */
    private array $hunks;
    
    /** @return Generator<int, Hunk> */
    public function getRealHunks(): Generator {
        foreach ($this->hunks as $hunk) {
            if ($hunk->isReal()) {
                yield $hunk;
            }
        }
    }
    
    public function getRealHunkCount(): int {
        return $this->realHunkCount;
    }
    
    public function load(SplFileInfo $inFile): void {
        $input = new FileDataAccess($inFile, StreamWrapperInterface::MODE_OPEN_READONLY);
        
        if ($input->readInteger(self::SIZEOF_UINT) !== 0x000003F3) {
            throw new UnexpectedValueException('0x000003F3');
        }
        if ($input->readInteger(self::SIZEOF_UINT) !== 0x00000000) {
            throw new UnexpectedValueException('0x00000000');
        }
        
        $this->realHunkCount = $input->readInteger(self::SIZEOF_UINT);
        $this->totalHunkCount = $this->realHunkCount;
        
        if ($input->readInteger(self::SIZEOF_UINT) !== 0x00000000) {
            throw new UnexpectedValueException('0x00000000');
        }
        
        if ($input->readInteger(self::SIZEOF_UINT) !== $this->totalHunkCount - 1) {
            throw new UnexpectedValueException('totalHunkCount:' . ($this->totalHunkCount - 1));
        }
        
        $this->hunks = [];
        
        $realHunks = [];
        for ($i = 0; $i < $this->realHunkCount; $i++) {
            $header = $input->readInteger(self::SIZEOF_UINT);
            $hunk = new Hunk();
            $hunk->size = $header & 0x3FFFFFFF;
            $hunk->memoryFlags = $header & 0xC0000000;
            if ($hunk->memoryFlags === 0xC0000000) {
                $hunk->memoryFlags = $input->readInteger(self::SIZEOF_UINT) & 0x80000000;
                throw new UnexpectedValueException("This implementation can't deal with double-sized headers.");
            }
            $realHunks[] = $hunk;
        }
        
        for ($i = 0, $j = 0; $i < $this->totalHunkCount; $i++) {
            $type = $input->readInteger(self::SIZEOF_UINT) & 0x1fffffff;
            switch ($type) {
                case Hunk::TYPE_CODE:
                case Hunk::TYPE_DATA:
                    $hunk = $realHunks[$j++];
                    $hunk->type = $type;
                    $hunk->numEntries = $input->readInteger(self::SIZEOF_UINT);
                    $hunk->data = $input->readString($hunk->numEntries * 4);
                    $this->hunks[] = $hunk;
                    break;
                case Hunk::TYPE_BSS:
                    $hunk = $realHunks[$j++];
                    $hunk->type = $type;
                    $hunk->numEntries = $input->readInteger(self::SIZEOF_UINT);
                    $this->hunks[] = $hunk;
                    break;
                case Hunk::TYPE_RELOC32:
                    $start = $input->getPosition();
                    $entries = new Map();
                    
                    while (($offsetCount = $input->readInteger(self::SIZEOF_UINT)) !== 0) {
                        $hunkNumber = $input->readInteger(self::SIZEOF_UINT);
                        $list = [];
                        for ($o = 0; $o < $offsetCount; $o++) {
                            $list[] = $input->readInteger(self::SIZEOF_UINT);
                        }
                        $entries->put($hunkNumber, $list);
                    }
                    
                    $size = $input->getPosition() - $start;
                    
                    $this->hunks[] = Hunk::createReloc32($size, $entries);
                    $this->totalHunkCount++;
                    break;
                case Hunk::TYPE_END:
                    $this->hunks[] = Hunk::createEnd();
                    $this->totalHunkCount++;
                    break;
                default:
                    throw new UnexpectedValueException("Dunno what to do with hunk type '$type'");
            }
        }
        
        if ($input->getPosition() + self::SIZEOF_UINT === $inFile->getSize()) {
            $type = $input->readInteger(self::SIZEOF_UINT) & 0x1fffffff;
            if ($type !== Hunk::TYPE_END) {
                throw new UnexpectedValueException((string) $type);
            }
            $this->hunks[] = Hunk::createEnd();
        }
        
        if ($input->getPosition() !== $inFile->getSize()) {
            throw new UnexpectedValueException((string) $input->getPosition());
        }
    }
    
    // where in AM2 does it say "Ambermoon"?
    private const MAGIC_OFFSET = 6;
    
    private const MAGIC_LENGTH = 5;
    
    private const MAGIC_WORD = 'Amber';
    
    public function requiresDeploding(): bool {
        return $this->hunks[0]->type !== Hunk::TYPE_CODE or $this->hunks[0]->getDataString(self::MAGIC_OFFSET, self::MAGIC_LENGTH) !== self::MAGIC_WORD;
    }
    
    public function save(SplFileInfo $outFile): void {
        $outDirectory = FileInfoFactory::createFromPath($outFile->getPath());
        
        FileSystem::ensureDirectory((string) $outDirectory);
        
        $output = new FileDataAccess($outFile, StreamWrapperInterface::MODE_CREATE_WRITEONLY);
        
        $output->writeInteger(0x000003F3, self::SIZEOF_UINT);
        $output->writeInteger(0, self::SIZEOF_UINT);
        $output->writeInteger($this->realHunkCount, self::SIZEOF_UINT);
        $output->writeInteger(0, self::SIZEOF_UINT);
        $output->writeInteger($this->realHunkCount - 1, self::SIZEOF_UINT);
        
        foreach ($this->hunks as $hunk) {
            if ($hunk->isReal()) {
                $header = $hunk->size | $hunk->memoryFlags;
                $output->writeInteger($header, self::SIZEOF_UINT);
            }
        }
        
        $lastHunk = null;
        foreach ($this->hunks as $hunk) {
            $output->writeInteger($hunk->type, self::SIZEOF_UINT);
            
            switch ($hunk->type) {
                case Hunk::TYPE_CODE:
                case Hunk::TYPE_DATA:
                    $output->writeInteger($hunk->numEntries, self::SIZEOF_UINT);
                    $output->writeString($hunk->data);
                    break;
                case Hunk::TYPE_BSS:
                    $output->writeInteger($hunk->numEntries, self::SIZEOF_UINT);
                    break;
                case Hunk::TYPE_RELOC32:
                    $start = $output->getPosition();
                    foreach ($hunk->entries as $hunkNumber => $entry) {
                        $output->writeInteger(count($entry), self::SIZEOF_UINT);
                        $output->writeInteger($hunkNumber, self::SIZEOF_UINT);
                        foreach ($entry as $offset) {
                            $output->writeInteger($offset, self::SIZEOF_UINT);
                        }
                    }
                    $output->writeInteger(0, self::SIZEOF_UINT);
                    $size = $output->getPosition() - $start;
                    if ($size !== $hunk->size) {
                        throw new UnexpectedValueException("RELOC32 has size '$hunk->size', but entries are $size bytes long.");
                    }
                    break;
                case Hunk::TYPE_END:
                    break;
                default:
                    throw new UnexpectedValueException("Dunno what to do with hunk type '$hunk->type'");
            }
            $lastHunk = $hunk;
        }
        
        if ($lastHunk and $lastHunk->type !== Hunk::TYPE_END) {
            $output->writeInteger(Hunk::TYPE_END, self::SIZEOF_UINT);
        }
    }
    
    public function deplode(bool $loadDeplodedData = true, bool $replaceImplodedHunks = true): void {
        $this->loadDeplodeInfo();
        
        if (! $loadDeplodedData) {
            return;
        }
        
        $temp = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        if ($temp === false) {
            throw new RuntimeException('Could not open a temporary stream for deploded data.');
        }
        
        try {
            $tempAccess = new ResourceDataAccess($temp);
            $this->loadDeplodedData($tempAccess);
            
            if ($replaceImplodedHunks) {
                $this->hunks = self::createDeplodedHunks($tempAccess, $this->deplodedSize, $this->deplodedHunkSizes, $this->deplodedMemFlags);
                $this->totalHunkCount = count($this->hunks);
                $this->realHunkCount = 0;
                foreach ($this->hunks as $hunk) {
                    if ($hunk->isReal()) {
                        $this->realHunkCount++;
                    }
                }
            }
        } finally {
            fclose($temp);
        }
    }
    
    /** @var list<int> */
    public array $deplodedHunkSizes;
    
    /** @var list<int> */
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
        foreach ($this->hunks as $hunk) {
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
        
        for ($i = 0; $i < 8; $i++) {
            $this->deplodeInfo->matchBase[] = $lastCodeHunk->getDataInteger(0x188 + $i * 2, 2);
        }
        
        for ($i = 0; $i < 12; $i++) {
            $this->deplodeInfo->matchExtra[] = $lastCodeHunk->getDataInteger(0x188 + 16 + $i);
        }
        
        $this->deplodeInfo->firstLiteralLength = $lastCodeHunk->getDataInteger(0x1E6, 2);
        $this->deplodeInfo->initialBitBuffer = $lastCodeHunk->getDataInteger(0x1E8);
        $this->deplodeInfo->implodedSize = $lastCodeHunk->getDataInteger(8, 4);
        
        $this->lastDataHunk = $lastDataHunk;
    }
    
    private function loadDeplodedData(DataAccessInterface $output): void {
        $lastDataHunkAccess = new HunkDataAccess($this->lastDataHunk);
        
        self::deplodeData($lastDataHunkAccess, $output, $this->deplodeInfo);
        
        $this->deplodedSize = $output->getPosition();
    }
    
    private const DEPLODE_LITERAL_BASE = [
        6,
        10,
        10,
        18
    ];
    
    private const DEPLODE_LITERAL_EXTRA_BITS = [
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
        $bits = new BitReader($reverseInput, $info->initialBitBuffer); // byte at offset 0x1E8 in the last code hunk
        
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
            if ($bits->read(1) !== 0) {
                if ($bits->read(1) !== 0) {
                    if ($bits->read(1) !== 0) {
                        $selector = 3;
                        
                        if ($bits->read(1) !== 0) {
                            if ($bits->read(1) !== 0) { // 11111
                                $matchLength = $reverseInput->readInteger(self::SIZEOF_BYTE);
                            } else { // 11110
                                $matchLength = 6 + $bits->read(3);
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
            if ($bits->read(1) !== 0) {
                if ($bits->read(1) !== 0) { // 11
                    $y = self::DEPLODE_LITERAL_BASE[$x];
                    $x += 8;
                } else { // 10
                    $y = 2;
                    $x += 4;
                }
            }
            $x = self::DEPLODE_LITERAL_EXTRA_BITS[$x];
            
            /* next literal run length: read [x] bits and add [y] */
            $literalLength = $y + $bits->read($x);
            
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
            if ($bits->read(1) !== 0) {
                if ($bits->read(1) !== 0) {
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
            $match -= $bits->read($x);
            
            $currentOutput->setPosition($match);
            
            $availableLength = $output->getPosition() - $match;
            
            /* copy match */
            while ($matchLength > 0) {
                // $match may include output yet to be written, so only write as much as is available
                $deltaLength = min($matchLength, $availableLength);
                $output->writeString($currentOutput->readString($deltaLength));
                $matchLength -= $deltaLength;
            }
        }
    }
    
    /**
     * @param list<int> $deplodedHunkSizes
     * @param list<int> $deplodedMemFlags
     * @return list<Hunk>
     */
    public static function createDeplodedHunks(DataAccessInterface $deploded, int $deplodedSize, array $deplodedHunkSizes, array $deplodedMemFlags): array {
        $deploded->setPosition(0);
        
        $eof = function (int $sizeLeft = 0) use ($deploded, $deplodedSize): bool {
            return $deploded->getPosition() + $sizeLeft >= $deplodedSize;
        };
        
        $hunks = [];
        
        $hunkSizeIndex = 0;
        
        while (! $eof()) {
            $header = $deploded->readInteger(self::SIZEOF_UINT);
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
                    if ($hunkSize * 4 !== $deplodedHunkSizes[$hunkSizeIndex]) {
                        $hunkSize *= 4;
                        throw new UnexpectedValueException("Invalid hunk data size '$hunkSize', expected '{$deplodedHunkSizes[$hunkSizeIndex]}'.");
                    }
                    $hunks[] = Hunk::createCode($deplodedMemFlags[$hunkSizeIndex], $hunkSize, $deploded->readString($deplodedHunkSizes[$hunkSizeIndex]));
                    $hunkSizeIndex++;
                    break;
                case 1:
                    $start = $deploded->getPosition();
                    $entries = new Map();
                    
                    // Note: The imploder stores the reloc offsets as deltas!
                    
                    while (($offsetCount = $deploded->readInteger(self::SIZEOF_UINT)) !== 0) {
                        $currentOffset = 0;
                        $hunkNumber = $deploded->readInteger(self::SIZEOF_UINT);
                        $list = [];
                        for ($o = 0; $o < $offsetCount; $o++) {
                            $currentOffset += $deploded->readInteger(self::SIZEOF_UINT);
                            $list[] = $currentOffset;
                        }
                        $entries->put($hunkNumber, $list);
                    }
                    
                    $size = $deploded->getPosition() - $start;
                    
                    $hunks[] = Hunk::createReloc32($size, $entries, $hunkSizeIndex === 0 ? 0 : $deplodedMemFlags[$hunkSizeIndex - 1]);
                    break;
                case 2:
                case 3:
                    $hunkSize = $eof(self::SIZEOF_UINT) ? 0 : ($deploded->readInteger(self::SIZEOF_UINT, true) & 0x3fffffff);
                    if ($hunkSize > 0) {
                        // Data or AM2_BLIT Code
                        $deploded->readString(self::SIZEOF_UINT);
                        
                        if ($hunkSize * 4 !== $deplodedHunkSizes[$hunkSizeIndex]) {
                            $hunkSize *= 4;
                            throw new UnexpectedValueException("Invalid hunk data size '$hunkSize', expected '{$deplodedHunkSizes[$hunkSizeIndex]}'.");
                        }
                        
                        // this might very well be the second code hunk of AM2_BLIT!
                        $isCode = ($header === 2147483648 and $hunkSize === 0x01B1);
                        
                        if ($isCode) {
                            $hunks[] = Hunk::createCode($deplodedMemFlags[$hunkSizeIndex], $hunkSize, $deploded->readString($deplodedHunkSizes[$hunkSizeIndex]));
                        } else {
                            $hunks[] = Hunk::createData($deplodedMemFlags[$hunkSizeIndex], $hunkSize, $deploded->readString($deplodedHunkSizes[$hunkSizeIndex]));
                        }
                    } else {
                        // BSS
                        if ($hunkSizeIndex == count($deplodedHunkSizes)) {
                            break 2;
                        }
                        
                        $hunks[] = Hunk::createBSS($deplodedMemFlags[$hunkSizeIndex], $deplodedHunkSizes[$hunkSizeIndex] / 4);
                    }
                    
                    $hunkSizeIndex++;
                    break;
                default:
                    throw new UnexpectedValueException("Invalid hunk flag '$flags'.");
            }
            
            if (! $eof(self::SIZEOF_UINT)) {
                $nextHeader = $deploded->readInteger(self::SIZEOF_UINT, true);
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
        
        $lastHunk = end($hunks);
        if ($lastHunk !== false && $lastHunk->type === Hunk::TYPE_END) {
            array_pop($hunks);
        }
        
        return $hunks;
    }
}
