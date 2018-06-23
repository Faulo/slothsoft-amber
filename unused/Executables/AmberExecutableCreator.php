<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Executables;

use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Savegame\Executables\SavegameExecutableCreator;

class AmberExecutableCreator extends SavegameExecutableCreator
{

    public function createExecutableMerger(array $executables): ExecutableInterface
    {
        return $this->initExecutable(new ExecutableMerger($executables));
    }
}

