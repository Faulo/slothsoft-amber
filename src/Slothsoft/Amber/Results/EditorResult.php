<?php
namespace Slothsoft\Amber\Results;

use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Farah\Module\Results\ResultImplementation;
use DOMDocument;
use DOMElement;

class EditorResult extends ResultImplementation
{
    public function toElement(DOMDocument $targetDoc) : DOMElement
    {}
    
    public function toDocument() : DOMDocument
    {}

    public function toFile() : HTTPFile
    {}

    public function toString() : string
    {}
    
    public function exists() : bool
    {
        return true;
    }

}

