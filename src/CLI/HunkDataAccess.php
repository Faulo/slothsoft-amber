<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

final class HunkDataAccess extends ResourceDataAccess {
    
    private $resource;
    
    public function __construct(Hunk $hunk) {
        $this->resource = fopen('php://temp', StreamWrapperInterface::MODE_CREATE_READWRITE);
        fwrite($this->resource, $hunk->data);
        rewind($this->resource);
        
        parent::__construct($this->resource);
    }
    
    public function __destruct() {
        fclose($this->resource);
    }
}