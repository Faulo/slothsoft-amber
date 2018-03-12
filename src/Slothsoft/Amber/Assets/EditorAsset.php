<?php
namespace Slothsoft\Amber\Assets;

use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Amber\Controller\EditorController;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveBuilder;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveExtractor;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlPath;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\Node\Asset\AssetInterface;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Farah\Module\Results\DOMWriterResult;
use Slothsoft\Farah\Module\Results\ResultInterface;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveBuilder;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;

class EditorAsset extends AssetImplementation
{
    const PARAM_LOAD_FILE = 'LoadFile';
    const PARAM_SAVE_FILE = 'SaveFile';
    const PARAM_DOWNLOAD_FILE = 'DownloadFile';
    
    const PARAM_EDITOR_DATA = 'save';
    
    
    protected function loadParameterFilter() : ParameterFilterInterface{
        return new ParameterFilter([
            self::PARAM_LOAD_FILE => '',
            self::PARAM_SAVE_FILE => '',
            self::PARAM_DOWNLOAD_FILE => '',
            
            self::PARAM_EDITOR_DATA => [],
        ]);
    }
    protected function loadResult(FarahUrl $url): ResultInterface
    {
        $args = $url->getArguments();
        
        $controller = new EditorController();
        
        $editorConfig = $controller->createEditorConfig($args);
        
        $loadFile = $args->get(self::PARAM_LOAD_FILE);
        $saveFile = $args->get(self::PARAM_SAVE_FILE);
        $downloadFile = $args->get(self::PARAM_DOWNLOAD_FILE);
        
        $request = (array) $args->get(self::PARAM_EDITOR_DATA);
        
        if (isset($request['editor'])) {
            if (isset($request['editor']['archives'])) {
                foreach ($request['editor']['archives'] as $val) {
                    $editorConfig['selectedArchives'][$val] = true;
                }
            }
        }
        if ($loadFile) {
            $editorConfig['selectedArchives'][$loadFile] = true;
        }
        if ($saveFile) {
            $editorConfig['selectedArchives'][$saveFile] = true;
        }
        if ($downloadFile) {
            $editorConfig['selectedArchives'][$downloadFile] = true;
        }
        
        if (isset($_FILES['save'])) {
            foreach ($_FILES['save']['tmp_name'] as $file => $filepath) {
                if (strlen($filepath) and file_exists($filepath)) {
                    $editorConfig['uploadedArchives'][$file] = $filepath;
                }
            }
        }
        
        $editor = $controller->createEditor($editorConfig);
        
        $editor->parseRequest($request);
        
        return new DOMWriterResult($url, $editor);
    }
    
    private function getAmberAsset(string $path) : AssetInterface {
        return $this->getOwnerModule()->lookupAssetByPath(
            FarahUrlPath::createFromString($path)
        );
    }
    private function createAmbTool() : AmbTool {
        return new AmbTool(
            $this->getAmberAsset('/cli/ambtool')->getRealPath()
        );
    }
    private function createArchiveExtractors() : array {
        $ret = [];
        
        $amberExtractor = new AmberArchiveExtractor($this->createAmbTool());
        $ret[AmbTool::TYPE_AMBR] = $amberExtractor;
        $ret[AmbTool::TYPE_JH] = $amberExtractor;
        
        $copyExtractor = new CopyArchiveExtractor();
        $ret[AmbTool::TYPE_RAW] = $copyExtractor;
        $ret[AmbTool::TYPE_AM2] = $copyExtractor;
        
        return $ret;
    }
    private function createArchiveBuilders() : array {
        $ret = [];
        
        $amberBuilder = new AmberArchiveBuilder();
        $ret[AmbTool::TYPE_AMBR] = $amberBuilder;
        
        $copyBuilder = new CopyArchiveBuilder();
        $ret[AmbTool::TYPE_JH] = $copyBuilder;
        $ret[AmbTool::TYPE_RAW] = $copyBuilder;
        $ret[AmbTool::TYPE_AM2] = $copyBuilder;
        
        return $ret;
    }
}

