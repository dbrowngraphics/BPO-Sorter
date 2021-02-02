<?php

namespace BPO;

use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \ZipArchive;

class Zip
{
    private $config;

    private $directory;
    private $fileName;

    public function __construct($config)
    {
        $this->config = $config;

        $this->directory = $this->config->process . DIRECTORY_SEPARATOR . 'pdfs';
        $this->fileName  = 'PDFs-' . $this->config->timestamp . '.zip';

        $this->zip();
    }

    private function zip()
    {
        if (! extension_loaded('zip') ||
            ! file_exists($this->directory)) {

            return false;
        }

        $zip = new ZipArchive();

        if (! $zip->open($this->config->archive . DIRECTORY_SEPARATOR . $this->fileName, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $this->directory = str_replace('\\', '/', realpath($this->directory));

        if (true === is_dir($this->directory)) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                    continue;
                }

                $file = realpath($file);

                if (true === is_dir($file)) {
                    $zip->addEmptyDir(str_replace($this->directory . '/', '', $file . '/'));
                } elseif (true === is_file($file)) {
                    $zip->addFromString(str_replace($this->directory . '/', '', $file), file_get_contents($file));
                }
            }
        } elseif (true === is_file($this->directory)) {
            $zip->addFromString(basename($this->directory), file_get_contents($this->directory));
        }

        return $zip->close();
    }
}
