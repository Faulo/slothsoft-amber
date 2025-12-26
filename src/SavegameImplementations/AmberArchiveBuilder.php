<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\SavegameImplementations;

use Slothsoft\Savegame\Node\ArchiveParser\ArchiveBuilderInterface;

final class AmberArchiveBuilder implements ArchiveBuilderInterface {
    
    public function buildArchive(iterable $buildChildren): string {
        $header = [];
        $body = [];
        $maxId = 0;
        foreach ($buildChildren as $child) {
            $id = (int) $child->getFileName();
            if ($id > $maxId) {
                $maxId = $id;
            }
            $val = $child->getContent();
            $header[$id] = pack('N', strlen($val));
            $body[$id] = $val;
        }
        for ($id = 1; $id < $maxId; $id ++) {
            if (! isset($header[$id])) {
                $header[$id] = pack('N', 0);
                $body[$id] = '';
            }
        }
        ksort($header);
        ksort($body);
        
        array_unshift($header, 'AMBR' . pack('n', count($body)));
        
        return implode('', $header) . implode('', $body);
    }
}

