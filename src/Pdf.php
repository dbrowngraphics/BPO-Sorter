<?php

namespace BPO;

class Pdf
{
    private $config;

    private $source;
    private $target;

    public function __construct($config)
    {
        $this->config = $config;

        $this->source = $this->config->process . DIRECTORY_SEPARATOR . 'target' . DIRECTORY_SEPARATOR;
        $this->target = $this->config->process . DIRECTORY_SEPARATOR . 'pdfs' . DIRECTORY_SEPARATOR;

        $this->makePdfs();
    }

    private function makePdfs()
    {
        if (! is_dir($this->target)) {
            mkdir($this->target);
        }

        // TODO: use config info
        if (is_dir($this->source)) {
            if ($dh = opendir($this->source)) {
                while (false !== ($directory = readdir($dh))) {
                    if ('.'  !== $directory &&
                        '..' !== $directory &&
                        'unknown' !== $directory &&
                        is_dir($this->source . $directory)) {

                        $pages = [];

                        if ($directoryHandle = opendir($this->source . $directory)) {
                            while (false !== ($file = readdir($directoryHandle))) {
                                if ('.'  !== $file &&
                                    '..' !== $file &&
                                    ! is_dir($file)) {

                                    // Allow for multi-page per company and sorted by company name
                                    $pages[$this->getCompany($file) . '-' . $this->getPageNumber($file)] = $this->getPageNumber($file);
                                }
                            }

                            closedir($directoryHandle);
                        }

                        // Do Not Sort: Keep them in alphabetical order
                        // sort($pages);
                        // Were getting weird file order. Add company as key and sort by company.
                        ksort($pages);

                        $pdf = new \FPDI();

                        $pdf->setPrintHeader(false);
                        $pdf->setPrintFooter(false);

                        // TODO: use standard pdf file name
                        $pdf->setSourceFile($this->config->process . DIRECTORY_SEPARATOR . 'source.pdf');

                        foreach ($pages as $company => $page) {
                            $templateId = $pdf->importPage($page);

                            $pdf->AddPage('L');

                            $pdf->useTemplate($templateId);
                        }

                        $filename = $this->target . $directory . '-' . $this->config->timestamp . '.pdf';

                        $pdf->Output($filename, 'F');
                    }
                }

                closedir($dh);
            }
        }

        // Do the same for the unknown folder
        if (is_dir($this->source . 'unknown')) {
            if ($dh = opendir($this->source . 'unknown')) {
                while (false !== ($file = readdir($dh))) {
                    if ('.'  !== $file &&
                        '..' !== $file &&
                        ! is_dir($file)) {

                        $pdf = new \FPDI();

                        $pdf->setPrintHeader(false);
                        $pdf->setPrintFooter(false);

                        $pdf->setSourceFile($this->config->process . DIRECTORY_SEPARATOR . 'source.pdf');

                        $templateId = $pdf->importPage($this->getPageNumber($file));

                        $pdf->AddPage('L');

                        $pdf->useTemplate($templateId);

                        // TODO: dynamic output file name
                        $filename = $this->target . 'unknown-' . $this->getPageNumber($file) . '-' . $this->config->timestamp . '.pdf';

                        $pdf->Output($filename, 'F');
                    }
                }

                closedir($dh);
            }
        }
    }

    private function getCompany($file)
    {
        $parts = explode('-', $file);

        return $parts[0];
    }

    private function getPageNumber($file)
    {
        $parts = explode('-', explode('.', $file)[0]);

        return $parts[1];
    }
}
