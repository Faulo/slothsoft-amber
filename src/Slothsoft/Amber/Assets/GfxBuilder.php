<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Core\ImageHelper;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileInfoResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use Slothsoft\Savegame\Node\ArchiveNode;
use Slothsoft\Savegame\Node\FileContainer;
use Slothsoft\Savegame\Node\ImageValue;
use SplFileInfo;

class GfxBuilder extends AbstractResourceBuilder
{
    protected function processInfoset(string $infosetId): ResultBuilderStrategyInterface
    {
        $this->editor->load();
        return new DOMWriterResultBuilder($this->editor);
    }
    protected function processArchive(string $infosetId, string $archiveId): ResultBuilderStrategyInterface
    {
        $gfxId = (int) $this->args->get(GfxParameterFilter::PARAM_GFX_ID);
        $paletteId = (int) $this->args->get(GfxParameterFilter::PARAM_PALETTE_ID);
        
        $this->editor->loadArchive($archiveId);
        $archiveNode = $this->editor->getArchiveNode($archiveId);
        
        $this->maxHeight = 0;
        $this->maxWidth = 0;
        
        return new FileInfoResultBuilder($this->createArchiveImage($archiveNode, $gfxId, $paletteId));
    }
    protected function processFile(string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface
    {
        $gfxId = (int) $this->args->get(GfxParameterFilter::PARAM_GFX_ID);
        $paletteId = (int) $this->args->get(GfxParameterFilter::PARAM_PALETTE_ID);
        
        $this->editor->loadArchive($archiveId);
        $archiveNode = $this->editor->getArchiveNode($archiveId);
        $fileNode = $archiveNode->getFileNodeByName($fileId);
        
        $this->maxHeight = 0;
        $this->maxWidth = 0;
        
        return new FileInfoResultBuilder($this->createFileImage($fileNode, $gfxId, $paletteId));
    }
    
    private $archiveWidth;
    private $archiveHeight;
    
    private $fileWidth;
    private $fileHeight;
    
    private $imageWidth;
    private $imageHeight;
    
    private function createArchiveImage(ArchiveNode $archiveNode, int $gfxId, int $paletteId): SplFileInfo {
        $imageFiles = [];
        
        $this->archiveWidth = 0;
        $this->archiveHeight = 0;
        
        foreach ($archiveNode->getFileNodes() as $fileNode) {
            $imageFiles[] = $this->createFileImage($fileNode, $gfxId, $paletteId);
            $this->archiveWidth = max($this->archiveWidth, $this->fileWidth);
            $this->archiveHeight = max($this->archiveHeight, $this->fileHeight);
        }
        
        $imageCount = count($imageFiles);
        
        $gfxFile = $imageFiles[0];
        $destFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/../$gfxId.$paletteId.png");
        
        $cols = $imageCount;
        $rows = 1;
        
        if (!$destFile->isFile()) {
            if ($imageCount === 1) {
                copy((string) $gfxFile, (string) $destFile);
            } else {
                ImageHelper::createSpriteSheet($destFile, $this->archiveWidth, $this->archiveHeight, $cols, $rows, ...$imageFiles);
            }
        }
        
        $this->archiveWidth *= $cols;
        $this->archiveHeight *= $rows;
        
        return $destFile;
    }
    private function createFileImage(FileContainer $fileNode, int $gfxId, int $paletteId): SplFileInfo {
        $imageFiles = [];
        
        $this->fileWidth = 0;
        $this->fileHeight = 0;
        
        if ($gfxId === -1) {
            foreach ($fileNode->getImageNodes() as $imageNode) {
                $imageFiles[] = $this->createImage($imageNode, $paletteId);
                $this->fileWidth = max($this->fileWidth, $this->imageWidth);
                $this->fileHeight = max($this->fileHeight, $this->imageHeight);
            }
        } else {
            $imageNode = $fileNode->getImageNodeById($gfxId);
            $imageFiles[] = $this->createImage($imageNode, $paletteId);
            $this->fileWidth = max($this->fileWidth, $this->imageWidth);
            $this->fileHeight = max($this->fileHeight, $this->imageHeight);
        }
        
        $imageCount = count($imageFiles);
        
        $cols = 1;
        $rows = $imageCount;
        
        $gfxFile = $imageFiles[0];
        $destFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/../$gfxId.$paletteId.png");
        
        if (!$destFile->isFile()) {
            if ($imageCount === 1) {
                copy((string) $gfxFile, (string) $destFile);
            } else {
                ImageHelper::createSpriteSheet($destFile, $this->fileWidth, $this->fileHeight, $cols, $rows, ...$imageFiles);
            }
        }
        
        $this->fileWidth *= $cols;
        $this->fileHeight *= $rows;
        
        return $destFile;
    }
    
    private function createImage(ImageValue $imageNode, int $paletteId): SplFileInfo {
        $imageFiles = [];
        
        $this->imageWidth = $imageNode->getWidth();
        $this->imageHeight = $imageNode->getHeight();
        
        if ($paletteId === -1) {
            foreach (range(0, 49) as $tmpPaletteId) {
                $imageFiles[] = $this->extractSingleImage($imageNode, $tmpPaletteId);
            }
        } else {
            $imageFiles[] = $this->extractSingleImage($imageNode, $paletteId);
        }
        
        $imageCount = count($imageFiles);
        
        $cols = $imageCount;
        $rows = 1;
            
        $gfxFile = $imageFiles[0];
        $destFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/$paletteId.png");
        
        if (!$destFile->isFile()) {
            if ($imageCount === 1) {
                copy((string) $gfxFile, (string) $destFile);
            } else {
                ImageHelper::createSpriteSheet($destFile, $this->imageWidth, $this->imageHeight, $cols, $rows, ...$imageFiles);
            }
        }
        
        $this->imageWidth *= $cols;
        $this->imageHeight *= $rows;
        
        return $destFile;
    }
    
    private function extractSingleImage(ImageValue $image, int $paletteId): SplFileInfo {
        $controller = new EditorController();
        $ambGfx = $controller->createAmbGfx();
        
        $gfxFile = $image->toFile();
        $tgaFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/../ambgfx/{$gfxFile->getFilename()}/{$image->getImageId()}/$paletteId.tga");
        $pngFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/../iview/{$gfxFile->getFilename()}/{$image->getImageId()}/$paletteId.png");
        
        if (!$pngFile->isFile()) {
            if (!$tgaFile->isFile()) {
                $ambGfx->extractTga($gfxFile, $tgaFile, $image->getWidth(), $image->getBitplanes(), $image->getContentOffset(), $image->getContentSize(), $paletteId);
            }
            ImageHelper::convertToPng($tgaFile, $pngFile, 0);
        }
        
        return $pngFile;
    }
}

