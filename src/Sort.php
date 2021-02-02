<?php

namespace BPO;

use BPO\File;

class Sort
{
    private $config;
    private $lookup;

    private $source;

    public function __construct($config, $lookup)
    {
        $this->config = $config;
        $this->lookup = $lookup;

        $this->source = $this->config->process . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR;

        $this->process();
    }

    /**
     * [process description]
     *
     * @return [type]       [description]
     */
    private function process()
    {
        if (is_dir($this->source)) {
            if ($dh = opendir($this->source)) {
                while (false !== ($file = readdir($dh))) {
                    if ('.'  !== $file &&
                        '..' !== $file &&
                        ! is_dir($file)) {

                        $extension = $this->getFileExtension($file);

                        if (in_array($extension, ['jpg', 'png'])) {
                            $newFile = new File($this->config, $file);

                            list($fileName, $node) = $this->sort($newFile);

                            if (! empty($node)) {
                                $newFile->move($fileName, $node);
                            }
                        }

                        unlink($this->source . $file);
                    }
                }

                closedir($dh);
            }
        }
    }

    /**
     * OCR given file, loop over all our known Group Ids looking for that string in the ORC text.
     *
     * @param  [type] $file [description]
     *
     * @return array        File Name, Node
     */
    private function sort($file)
    {
        $ocr = $file->getOCR();

        $node     = '';
        $fileName = '';

        // Ignore Invoice documents
        if (preg_match('/Summary Report/i', $ocr)) {
            $found = false;

            // Elapsed: 0d 0h 4m 43s 689msFinished
            // $positions = [];

            // All our Group Ids start with '01'
            // $needle = '01';
            // $last   = 0;

            // while (($last = strpos($ocr, $needle, $last)) !== false) {
            //     $positions[] = $last;
            //     $last = $last + strlen($needle);
            // }

            // foreach ($positions as $value) {
            //     $groupId = substr($ocr, $value, 7);

            //     if (ctype_digit($groupId)) {
            //         if (in_array($groupId, array_keys($this->lookup))) {
            //             $found = true;

            //             $node = $this->lookup[$groupId]['node'];

            //             // Need these to be alphabetized
            //             // [Group Name]-[Page #].[Ext]
            //             $fileName = $this->lookup[$groupId]['group'] . '-' . $file->page . '.' . $file->ext;

            //             break;
            //         }
            //     }
            // }

            // Elapsed: 0d 0h 4m 42s 690msFinished
            foreach ($this->lookup as $id => $info) {
                if (preg_match('/' . $id . '/i', $ocr)) { // strpos didn't work
                    $found = true;

                    $node  = $info['node'];

                    // Need these to be alphabetized
                    // [Group Name]-[Page #].[Ext]
                    $fileName = $info['group'] . '-' . $file->page . '.' . $file->ext;

                    break;
                }
            }

            if (! $found) {
                $node = 'unknown';

                // [Original File Name]-[Page #].[Ext]
                $fileName = $this->getFileName($file->name) . '-' . $file->page . '.' . $file->ext;
            }
        }

        return [$fileName, $node];
    }

    private function getFileName($file)
    {
        $parts = explode('-', explode('.', $file)[0]);

        return $parts[0];
    }

    private function getFileExtension($filename)
    {
        $extension = explode('.', $filename);

        return strtolower(array_pop($extension));
    }
}
