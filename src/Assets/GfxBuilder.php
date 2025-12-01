<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\ParameterFilters\GfxParameterFilter;
use Slothsoft\Core\ImageHelper;
use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromElementDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\FileInfoResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\ResultBuilderStrategyInterface;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\Node\ArchiveNode;
use Slothsoft\Savegame\Node\FileContainer;
use Slothsoft\Savegame\Node\ImageValue;
use DOMDocument;
use DOMElement;
use Imagick;
use ImagickException;
use SplFileInfo;
use Slothsoft\Amber\CLI\AmbGfx;

class GfxBuilder implements ExecutableBuilderStrategyInterface {
    
    private AssetInterface $asset;
    
    private FarahUrlArguments $args;
    
    private Editor $editor;
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        if (! AmbGfx::isSupported()) {
            return new ExecutableStrategies(new NullResultBuilder());
        }
        
        $game = $args->get(GfxParameterFilter::PARAM_GAME);
        $version = $args->get(GfxParameterFilter::PARAM_VERSION);
        $user = $args->get(GfxParameterFilter::PARAM_USER);
        
        $infosetId = $args->get(GfxParameterFilter::PARAM_INFOSET_ID);
        $archiveId = $args->get(GfxParameterFilter::PARAM_ARCHIVE_ID);
        $fileId = $args->get(GfxParameterFilter::PARAM_FILE_ID);
        
        $controller = new EditorController();
        
        $config = $controller->createEditorConfig($game, $version, $user, $infosetId);
        
        $this->asset = $context;
        $this->args = $args;
        $this->editor = $controller->createEditor($config);
        
        if ($fileId === '') {
            if ($archiveId === '') {
                $resultBuilder = $this->processInfoset($infosetId);
            } else {
                $resultBuilder = $this->processArchive($infosetId, $archiveId);
            }
        } else {
            $resultBuilder = $this->processFile($infosetId, $archiveId, $fileId);
        }
        
        return new ExecutableStrategies($resultBuilder);
    }
    
    private function processInfoset(string $infosetId): ResultBuilderStrategyInterface {
        return new DOMWriterResultBuilder(new DOMWriterFromElementDelegate(function (DOMDocument $document) use ($infosetId): DOMElement {
            $root = $document->createElement('infoset');
            $root->setAttribute('id', $infosetId);
            $root->textContent = 'Must provide archiveId.';
            return $root;
        }), 'infoset.xml');
    }
    
    private function processArchive(string $infosetId, string $archiveId): ResultBuilderStrategyInterface {
        $gfxId = $this->args->get(GfxParameterFilter::PARAM_GFX_ID);
        $paletteId = $this->args->get(GfxParameterFilter::PARAM_PALETTE_ID);
        
        $this->editor->loadArchive($archiveId);
        $archiveNode = $this->editor->getArchiveNode($archiveId);
        
        return new FileInfoResultBuilder($this->createArchiveImage($archiveNode, $gfxId, $paletteId));
    }
    
    private function processFile(string $infosetId, string $archiveId, string $fileId): ResultBuilderStrategyInterface {
        $gfxId = $this->args->get(GfxParameterFilter::PARAM_GFX_ID);
        $paletteId = $this->args->get(GfxParameterFilter::PARAM_PALETTE_ID);
        
        $this->editor->loadArchive($archiveId);
        $archiveNode = $this->editor->getArchiveNode($archiveId);
        $fileNode = $archiveNode->getFileNodeByName($fileId);
        
        return new FileInfoResultBuilder($this->createFileImage($fileNode, $gfxId, $paletteId));
    }
    
    private $archiveWidth;
    
    private $archiveHeight;
    
    private $fileWidth;
    
    private $fileHeight;
    
    private $imageWidth;
    
    private $imageHeight;
    
    private function createArchiveImage(ArchiveNode $archiveNode, int $gfxId, int $paletteId): SplFileInfo {
        $archiveNode->load();
        
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
        
        if (! $destFile->isFile()) {
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
        $fileNode->load();
        
        $imageFiles = [];
        
        $this->fileWidth = 0;
        $this->fileHeight = 0;
        
        if ($gfxId === - 1) {
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
        
        if (! $destFile->isFile()) {
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
        $images = [];
        $image = null;
        
        $this->imageWidth = $imageNode->getWidth();
        $this->imageHeight = $imageNode->getHeight();
        
        if ($paletteId === - 1) {
            foreach (range(0, 49) as $tmpPaletteId) {
                $imageFiles[] = $this->extractSingleImage($imageNode, $tmpPaletteId, $image);
                $images[] = $image;
            }
        } else {
            $imageFiles[] = $this->extractSingleImage($imageNode, $paletteId, $image);
            $images[] = $image;
        }
        
        $imageCount = count($imageFiles);
        
        $cols = $imageCount;
        $rows = 1;
        
        $gfxFile = $imageFiles[0];
        $destFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/$paletteId.png");
        
        if (! $destFile->isFile()) {
            if ($imageCount === 1) {
                copy((string) $gfxFile, (string) $destFile);
            } else {
                ImageHelper::createSpriteSheetFromImages($destFile, $this->imageWidth, $this->imageHeight, $cols, $rows, ...$images);
            }
        }
        
        $this->imageWidth *= $cols;
        $this->imageHeight *= $rows;
        
        return $destFile;
    }
    
    private function extractSingleImage(ImageValue $image, int $paletteId, ?Imagick &$sprite): SplFileInfo {
        $controller = new EditorController();
        $ambGfx = $controller->createAmbGfx();
        
        $gfxFile = $image->toFile();
        $tgaFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/../ambgfx/{$gfxFile->getFilename()}/{$image->getImageId()}/$paletteId.tga");
        $pngFile = FileInfoFactory::createFromPath("{$gfxFile->getPath()}/../iview/{$gfxFile->getFilename()}/{$image->getImageId()}/$paletteId.png");
        
        $hasConverted = false;
        $hasExtracted = false;
        
        if (! $pngFile->isFile()) {
            if (! $tgaFile->isFile()) {
                $ambGfx->extractTga($gfxFile, $tgaFile, $image->getWidth(), $image->getBitplanes(), $image->getContentOffset(), $image->getContentSize(), $paletteId);
                $hasExtracted = true;
            }
            ImageHelper::convertToPng($tgaFile, $pngFile, 0);
            $hasConverted = true;
        }
        
        try {
            $sprite = new Imagick((string) $pngFile);
        } catch (ImagickException $e) {
            if (! $hasExtracted) {
                $ambGfx->extractTga($gfxFile, $tgaFile, $image->getWidth(), $image->getBitplanes(), $image->getContentOffset(), $image->getContentSize(), $paletteId);
            }
            if (! $hasConverted) {
                ImageHelper::convertToPng($tgaFile, $pngFile, 0);
            }
            $sprite = new Imagick((string) $pngFile);
        }
        
        return $pngFile;
    }
}

