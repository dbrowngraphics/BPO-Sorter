<?php

namespace BPO;

class Archive
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;

        $this->archiveFiles();
    }

    private function archiveFiles()
    {
        if (is_dir($this->config->source)) {
            if ($dh = opendir($this->config->source)) {
                while (false !== ($file = readdir($dh))) {
                    if ('.'  !== $file &&
                        '..' !== $file &&
                        ! is_dir($file)) {

                        $this->checkArchiveDirectory();

                        $extension = $this->getFileExtension($file);

                        // Archive Folder
                        if (in_array($extension, ['pdf', 'xls'])) {
                            copy($this->config->source . DIRECTORY_SEPARATOR . $file, $this->config->archive . DIRECTORY_SEPARATOR . $file);
                        }

                        // Process Folder
                        if ('pdf' == $extension) {
                            copy($this->config->source . DIRECTORY_SEPARATOR . $file, $this->config->process . DIRECTORY_SEPARATOR . 'source.pdf');
                        }

                        if ('xls' == $extension) {
                            copy($this->config->source . DIRECTORY_SEPARATOR . $file, $this->config->process . DIRECTORY_SEPARATOR . 'lookup.xls');
                        }

                        unlink($this->config->source . DIRECTORY_SEPARATOR . $file);
                    }
                }

                closedir($dh);
            }
        }
    }

    private function getFileExtension($filename)
    {
        $extension = explode('.', $filename);

        return strtolower(array_pop($extension));
    }

    private function checkArchiveDirectory()
    {
        if (! is_dir($this->config->archive)) {
            mkdir($this->config->archive);
        }
    }
}
