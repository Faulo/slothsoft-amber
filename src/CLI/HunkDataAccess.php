<?php
declare(strict_types = 1);

namespace Slothsoft\Amber\CLI;

use RuntimeException;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

final class HunkDataAccess extends ResourceDataAccess {
    
    public function __construct(Hunk $hunk) {
        $resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        if ($resource === false) {
            throw new RuntimeException('Could not open a temporary stream for hunk data.');
        }
        
        fwrite($resource, $hunk->data);
        rewind($resource);
        parent::__construct($resource);
    }
    
    public function __destruct() {
        fclose($this->resource);
    }
}