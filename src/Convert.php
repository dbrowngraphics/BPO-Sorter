<?php

namespace BPO;

class Convert
{
    // TODO: make dynamic
    private $limit  = 400;
    private $file   = 'source.pdf';
    private $filename = 'file';

    private $config;
    private $target;

    public function __construct($config)
    {
        $this->config = $config;
        $this->target = $this->config->process . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR;

        $this->convert();
    }

    private function convert()
    {
        if (! is_dir($this->target)) {
            mkdir($this->target);
        }

        // http://codetheory.in/convert-split-pdf-files-into-images-with-imagemagick-and-ghostscript/
        for ($i = 0; $i <= $this->limit; $i++) {
            exec('convert -density 150 ' . $this->config->process . DIRECTORY_SEPARATOR . $this->file . '[' . $i . '] -monochrome ' . $this->target . $this->filename . '-' . $i . '.jpg');
        }

        // unlink($this->config->process . DIRECTORY_SEPARATOR . $this->file);
    }
}
