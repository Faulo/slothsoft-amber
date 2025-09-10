<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;
use RuntimeException;
use SplFileInfo;

class AmbGfx {

    public static function isSupported(): bool {
        return PHP_OS_FAMILY === 'Windows' or FileSystem::commandExists('wine');
    }

    private $ambgfxPath;

    public function __construct(string $ambgfxPath) {
        assert(file_exists($ambgfxPath), "ambgfx not found at $ambgfxPath");

        $this->ambgfxPath = $ambgfxPath;
    }

    private function exec(string $file, array $args): void {
        $command = [];

        $command[] = $this->ambgfxPath;
        $command[] = $file;

        foreach ($args as $key => $value) {
            $command[] = "-$key";
            $command[] = $value;
        }

        $process = new WineProcess($command);
        $process->run();

        if ($process->getExitCode() !== 0) {
            throw new RuntimeException("amgfx failed!" . PHP_EOL . '> ' . $process->getCommandLine() . PHP_EOL . $process->getErrorOutput() . PHP_EOL . $process->getOutput());
        }
    }

    public function extractTga(SplFileInfo $inFile, SplFileInfo $outFile, int $width = 32, int $bitplanes = 5, int $offset = 0, int $size = 0, int $palette = 49, int $firstColor = 0): void {
        $outDirectory = FileInfoFactory::createFromPath($outFile->getPath());

        FileSystem::ensureDirectory((string) $outDirectory);

        $options = [
            'out' => (string) $outFile,
            'width' => $width,
            'bpl' => $bitplanes,
            'off' => $offset,
            'pal' => $palette,
            'col' => $firstColor
        ];
        if ($size > 0) {
            $options['size'] = $size;
        }

        $this->exec($inFile->getRealPath(), $options);
    }
}

