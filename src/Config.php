<?php

namespace BPO;

class Config
{
    public $timestamp;

    public $source;
    public $process;
    public $archive;

    public function __construct($source = 'drop', $process = 'process', $archive = 'archive')
    {
        $this->timestamp = date('YmdHis');

        $this->source  = getcwd() . DIRECTORY_SEPARATOR . $source;
        $this->process = getcwd() . DIRECTORY_SEPARATOR . $process;
        $this->archive = $this->process . DIRECTORY_SEPARATOR . $archive . DIRECTORY_SEPARATOR . $this->timestamp;
    }
}
