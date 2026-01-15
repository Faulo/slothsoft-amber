<?php
declare(strict_types = 1);
namespace Slothsoft\Amber\Controller;

use Slothsoft\Amber\ParameterFilters\EditorParameterFilter;
use Slothsoft\Core\ServerEnvironment;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use SplFileInfo;

final class EditorParameters {
    
    private string $repository;
    
    private string $game;
    
    private string $version;
    
    private string $user;
    
    private string $infoset;
    
    public function __construct(string $repository, string $game, string $version, string $user, string $infoset) {
        $this->repository = $repository;
        $this->game = $game;
        $this->version = $version;
        $this->user = $user;
        $this->infoset = $infoset;
    }
    
    public function withInfoset(string $infoset): self {
        return $this->infoset === $infoset ? $this : new self($this->repository, $this->game, $this->version, $this->user, $infoset);
    }
    
    private ?FarahUrl $repositoryUrl = null;
    
    public function getRepositoryUrl(): FarahUrl {
        return $this->repositoryUrl ??= FarahUrl::createFromReference($this->repository);
    }
    
    private ?FarahUrl $sourceUrl = null;
    
    public function getRepositorySourceUrl(): FarahUrl {
        return $this->sourceUrl ??= FarahUrl::createFromReference("$this->game/$this->version", $this->getRepositoryUrl());
    }
    
    public function getRepositorySourceDirectory(): SplFileInfo {
        return Module::resolveToAsset($this->getRepositorySourceUrl())->getFileInfo();
    }
    
    public function getServerDataDirectory(): SplFileInfo {
        return new SplFileInfo(ServerEnvironment::getDataDirectory() . "/slothsoft/amber/$this->game/$this->version/$this->user");
    }
    
    public function getServerCacheDirectory(): SplFileInfo {
        return new SplFileInfo(ServerEnvironment::getCacheDirectory() . "/slothsoft/amber/$this->game/$this->version/$this->user");
    }
    
    private ?FarahUrl $amberUrl = null;
    
    private function getAmberUrl(): FarahUrl {
        return $this->amberUrl ??= FarahUrl::createFromReference('farah://slothsoft@amber');
    }
    
    private ?FarahUrlArguments $amberArgs = null;
    
    private function getAmberArgs(): FarahUrlArguments {
        return $this->amberArgs ??= FarahUrlArguments::createFromValueList([
            EditorParameterFilter::PARAM_REPOSITORY => $this->repository,
            EditorParameterFilter::PARAM_GAME => $this->game,
            EditorParameterFilter::PARAM_VERSION => $this->version,
            EditorParameterFilter::PARAM_USER => $this->user,
            EditorParameterFilter::PARAM_INFOSET_ID => $this->infoset
        ]);
    }
    
    private ?FarahUrl $convertUrl = null;
    
    public function getStaticConvertUrl(): FarahUrl {
        return $this->convertUrl ??= FarahUrl::createFromReference("/templates/$this->game/convert/$this->infoset", $this->getAmberUrl());
    }
    
    private ?FarahUrl $infosetUrl = null;
    
    public function getStaticInfosetUrl(): FarahUrl {
        return $this->infosetUrl ??= FarahUrl::createFromReference("/templates/$this->game/infoset/$this->infoset", $this->getAmberUrl());
    }
    
    public function getStaticInfosetFile(): SplFileInfo {
        return Module::resolveToAsset($this->getStaticInfosetUrl())->getFileInfo();
    }
    
    private ?FarahUrl $editorTemplateUrl = null;
    
    public function getStaticEditorTemplateUrl(): FarahUrl {
        return $this->editorTemplateUrl ??= FarahUrl::createFromReference("/templates/$this->game/editor/$this->infoset", $this->getAmberUrl());
    }
    
    private ?FarahUrl $dictionaryTemplateUrl = null;
    
    public function getStaticDictionaryTemplateUrl(): FarahUrl {
        return $this->dictionaryTemplateUrl ??= FarahUrl::createFromReference("/xsl/dictionary", $this->getAmberUrl());
    }
    
    private ?FarahUrl $datasetUrl = null;
    
    public function getProcessDatasetUrl(): FarahUrl {
        return $this->datasetUrl ??= $this->getAmberUrl()
            ->withPath("/api/dataset")
            ->withQueryArguments($this->getAmberArgs());
    }
    
    private ?FarahUrl $amberdataUrl = null;
    
    public function getProcessAmberdataUrl(): FarahUrl {
        return $this->amberdataUrl ??= $this->getAmberUrl()
            ->withPath("/api/amberdata")
            ->withQueryArguments($this->getAmberArgs());
    }
    
    private ?FarahUrl $dictionaryUrl = null;
    
    public function getProcessDictionaryUrl(): FarahUrl {
        return $this->dictionaryUrl ??= $this->getAmberUrl()
            ->withPath("/api/dictionary")
            ->withQueryArguments($this->getAmberArgs());
    }
}

