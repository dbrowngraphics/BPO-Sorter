<?php

namespace BPO;

use \PhpOffice\PhpSpreadsheet\IOFactory;

class Lookup
{
    public $reference = [];

    private $file = 'lookup.xls';

    private $config;

    public function __construct($config)
    {
        $this->config = $config;

        $this->loadSP();
    }

    /**
     * [load description]
     *
     * @return void         [description]
     */
    private function load()
    {
        die('This should not run!');
        $spreadsheet = IOFactory::load($this->config->process . DIRECTORY_SEPARATOR . $this->file);

        // $spreadsheet = IOFactory::load(getcwd() . DIRECTORY_SEPARATOR . 'process' . DIRECTORY_SEPARATOR . $this->file)

        // TODO: comment these parameters...no idea what they mean.
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Columns
        // 'A' => 'NODE'
        // 'B' => 'CIGNA_GROUP_ID'
        // 'C' => 'GROUP_NAME'
        // 'D' => 'GROUP_ID'

        foreach ($sheetData as $row) {
            $group = trim($row['C']);
            $group = str_replace(' ', '', $group);
            $group = str_replace('&', '', $group);
            $group = str_replace(',', '', $group);
            $group = str_replace('.', '', $group);
            $group = str_replace('-', '', $group);
            $group = str_replace('/', '', $group);
            $group = str_replace('?', '', $group);
            $group = str_replace("'", '', $group);

            $this->reference[$row['B']] = ['node' => $row['A'], 'group' => $group];
        }

        // Remove header row - 'CIGNA_GROUP_ID'
        array_shift($this->reference);

        var_dump($this->reference);
        die('Reference');

        // unlink($this->config->process . DIRECTORY_SEPARATOR . $this->file);
    }

    private function loadSP()
    {
        $conn = oci_connect('cw', 'todoall', 'NO1_CWP1.CWIBENEFITS.COM');

        $procedure = "BEGIN v3p_cigna_pbm_sort(); END;";

        $stmt = oci_parse($conn, $procedure);
        $stid = oci_parse($conn, 'SELECT * FROM v3t_cigna_sort');

        oci_execute($stmt);
        oci_execute($stid);

        while ($row = oci_fetch_object($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $group = trim($row->GROUP_NAME);
            $group = str_replace(' ', '', $group);
            $group = str_replace('&', '', $group);
            $group = str_replace(',', '', $group);
            $group = str_replace('.', '', $group);
            $group = str_replace('-', '', $group);
            $group = str_replace('/', '', $group);
            $group = str_replace('?', '', $group);
            $group = str_replace("'", '', $group);

            $this->reference[$row->CIGNA_GROUP_ID] = ['node'=> $row->NODE, 'group' => $group];
        }

    }
}
