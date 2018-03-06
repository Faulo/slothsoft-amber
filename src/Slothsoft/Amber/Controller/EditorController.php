<?php
namespace Slothsoft\Amber\Controller;

use Slothsoft\Amber\CLI\AmbGfx;
use Slothsoft\Amber\CLI\AmbTool;
use Slothsoft\Amber\Mod\ParameterFilter;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveBuilder;
use Slothsoft\Amber\SavegameImplementations\AmberArchiveExtractor;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlAuthority;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlPath;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetInterface;
use Slothsoft\Savegame\Editor;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveBuilder;
use Slothsoft\Savegame\Node\ArchiveParser\CopyArchiveExtractor;

class EditorController
{
    public function createEditorConfig(FarahUrlArguments $args) : array {
        $game = $args->get(ParameterFilter::PARAM_GAME);
        $mod = $args->get(ParameterFilter::PARAM_MOD);
        $struc = $args->get(ParameterFilter::PARAM_STRUCTURE);
        
        $mode = $args->get(ParameterFilter::PARAM_SAVE_MODE);
        $mode = preg_replace('~[^\w]~', '', $mode);
        $name = $args->get(ParameterFilter::PARAM_SAVE_ID);
        $name = preg_replace('~[^\w]~', '', $name);
        
        $editorConfig = [];
        $editorConfig['structureFile'] = $this->getAmberAsset("/games/$game/$struc")->getRealPath();
        $editorConfig['defaultDir'] = $this->getAmberAsset("/games/$game/mods/$mod/src")->getRealPath();
        $editorConfig['tempDir'] = $this->getAmberAsset("/games/$game/mods/$mod/tmp")->getRealPath();
        
        $editorConfig['mode'] = $mode;
        $editorConfig['id'] = $name;
        $editorConfig['loadAllArchives'] = false;
        $editorConfig['selectedArchives'] = [];
        $editorConfig['uploadedArchives'] = [];
        $editorConfig['archiveExtractors'] = $this->createArchiveExtractors();
        $editorConfig['archiveBuilders'] = $this->createArchiveBuilders();
        
        return $editorConfig;
    }
    public function createEditor(array $editorConfig) : Editor {
        $editor = new Editor($editorConfig);
        $editor->load();
        return $editor;
    }
    private function getAmberAsset(string $path) : AssetInterface {
        return FarahUrlResolver::resolveToAsset(
            FarahUrl::createFromComponents(
                FarahUrlAuthority::createFromVendorAndModule('slothsoft', 'amber'),
                FarahUrlPath::createFromString($path),
                FarahUrlArguments::createEmpty()
            )
        );
    }
    private function createAmbTool() : AmbTool {
        return new AmbTool(
            $this->getAmberAsset('/cli/ambtool')->getRealPath()
        );
    }
    private function createAmbGfx() : AmbGfx {
        return new AmbGfx(
            $this->getAmberAsset('/cli/ambgfx')->getRealPath()
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

