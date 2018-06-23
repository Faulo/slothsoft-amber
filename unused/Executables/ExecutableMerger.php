<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Executables;

use Slothsoft\Core\IO\Writable\DOMWriterElementFromDocumentTrait;
use Slothsoft\Farah\Module\Executables\ExecutableDOMWriterBase;
use DOMDocument;

class ExecutableMerger extends ExecutableDOMWriterBase
{
    use DOMWriterElementFromDocumentTrait;

    private $executables;

    public function __construct(array $executables)
    {
        $this->executables = $executables;
    }

    public function toDocument(): DOMDocument
    {
        if ($this->executables) {
            $resultDoc = null;
            foreach ($this->executables as $executable) {
                $tempDoc = $executable->lookupXmlResult()->toDocument();
                if ($resultDoc === null) {
                    $resultDoc = $tempDoc;
                } else {
                    $parentNode = $tempDoc->documentElement;
                    foreach ($parentNode->childNodes as $node) {
                        if ($node->nodeType === XML_ELEMENT_NODE) {
                            $resultDoc->documentElement->appendChild($resultDoc->importNode($node, true));
                        }
                    }
                }
            }
        } else {
            $resultDoc = new DOMDocument();
        }
        return $resultDoc;
    }
}

