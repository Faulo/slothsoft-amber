<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\CLI;

use RuntimeException;
use SplFileInfo;
use Slothsoft\Core\FileSystem;
use Slothsoft\Core\IO\FileInfoFactory;

class AmbGfx {

    public static function isSupported(): bool {
        return PHP_OS_FAMILY === 'Windows';
    }

    private $ambgfxPath;

    public function __construct(string $ambgfxPath) {
        assert(file_exists($ambgfxPath), "ambgfx not found at $ambgfxPath");

        $this->ambgfxPath = $ambgfxPath;
    }

    private function exec(string $file, array $args): string {
        $command = escapeshellarg($this->ambgfxPath) . ' ' . escapeshellarg($file);
        foreach ($args as $key => $val) {
            $command .= sprintf(' -%s %s', $key, escapeshellarg((string) $val));
        }
        $result = `$command`;
        return is_string($result) ? $result : json_encode($result);
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

        $result = $this->exec($inFile->getRealPath(), $options);

        if (! $outFile->isFile()) {
            throw new RuntimeException($result);
        }
    }
}

