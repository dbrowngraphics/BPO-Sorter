<?php

namespace BPO;

// use \TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOCR;

class File
{
    public $file;

    public $inputFile;

    public $name;
    public $page;
    public $ext;

    private $config;

    private $source;
    private $target;

    public function __construct($config, $file)
    {
        $this->config = $config;
        $this->file   = $file;

        $this->source = $this->config->process . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR;
        $this->target = $this->config->process . DIRECTORY_SEPARATOR . 'target' . DIRECTORY_SEPARATOR;

        $this->inputFile = $this->source . $this->file;

        $this->fileParts();
    }

    public function move($fileName, $node = 'unknown')
    {
        if (! is_dir($this->target)) {
            mkdir($this->target);
        }

        $outputDirectory = $this->target . $node;

        if (! is_dir($outputDirectory)) {
            mkdir($outputDirectory);
        }

        copy($this->inputFile, $outputDirectory . DIRECTORY_SEPARATOR . $fileName);

        return $this->inputFile . ' has been moved to: ' . $outputDirectory . DIRECTORY_SEPARATOR . $fileName . '<br>';
    }

    public function getOCR()
    {
        return (new TesseractOCR($this->inputFile))->run();
    }

    private function fileParts()
    {
        $parts = explode('.', $this->file);
        $this->name = $parts[0];
        $this->ext  = $parts[1];

        $parts2 = explode('-', $this->name);
        $this->page = (int) $parts2[1] + 1;
    }
}
