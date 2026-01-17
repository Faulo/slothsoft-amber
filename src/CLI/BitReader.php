<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

final class BitReader {
    
    private DataAccessInterface $reverseInput;
    
    private int $bitBuffer;
    
    public function __construct(DataAccessInterface $reverseInput, int $initialBitBuffer) {
        $this->reverseInput = $reverseInput;
        $this->bitBuffer = $initialBitBuffer;
    }
    
    public function read(int $count): int {
        $result = 0;
        
        if ($count >= 128) {
            $count -= 128;
            $result = $this->reverseInput->readInteger(1);
        }
        
        for ($i = 0; $i < $count; $i ++) {
            $bit = $this->bitBuffer >> 7;
            $this->bitBuffer = ($this->bitBuffer << 1) % 256;
            
            if ($this->bitBuffer == 0) {
                $temp = $bit;
                $this->bitBuffer = $this->reverseInput->readInteger(1);
                $bit = $this->bitBuffer >> 7;
                $this->bitBuffer = ($this->bitBuffer << 1) % 256;
                
                if ($temp !== 0) {
                    $this->bitBuffer ++;
                }
            }
            
            $result <<= 1;
            $result |= $bit;
        }
        
        return $result;
    }
}