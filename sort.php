<?php
set_time_limit(1200); // 20 Minutes

require __DIR__ . '/vendor/autoload.php';

use BPO\Archive;
use BPO\Convert;
use BPO\Config;
use BPO\Lookup;
use BPO\Pdf;
use BPO\Sort;
use BPO\Zip;

// $email = $argv[1];

$email = isset($_POST['email']) ? $_POST['email'] : 'daniel.brown@cwibenefits.com';
$location = isset($_POST['location']) ? $_POST['location'] : '2020/no_location_given';


// TODO: move to helper file (or config Class)
function delTree($dir)
{
    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }

    return rmdir($dir);
}

function moveFiles($dir, $location)
{
	$files = array_diff(scandir($dir), ['.', '..']);
	// Accounting Department\Cigna PBM Invoices\
	// \(space) used to escape the blank space between words

    $toDir = '../../../../mnt/ImagineExports/Accounting\ Department/Cigna\ PBM\ Invoices/' . $location;

    exec('mkdir -p ' . $toDir);

	foreach ($files as $file) {
		var_dump($file);        
        $from = $dir . DIRECTORY_SEPARATOR . $file;
        
        $to = $toDir . DIRECTORY_SEPARATOR . $file;
        exec('mv ' . $from . ' ' . $to);
    }
}

$dir = getcwd() . DIRECTORY_SEPARATOR . 'process' . DIRECTORY_SEPARATOR . 'pdf';

$config = new Config();

new Archive($config);
new Convert($config);

// TODO: move this into Sort class
$lookup = new Lookup($config);
new Sort($config, $lookup->reference);

new Pdf($config);

unlink($config->process . DIRECTORY_SEPARATOR . 'source.pdf');

// new Zip($config);

delTree($config->process . DIRECTORY_SEPARATOR . 'source');
delTree($config->process . DIRECTORY_SEPARATOR . 'target');


$sortedLocation = $location . '/sorted';
moveFiles($config->process . DIRECTORY_SEPARATOR . 'pdfs', $sortedLocation);

delTree($config->process . DIRECTORY_SEPARATOR . 'pdfs');

$fileLocation = 'Accounting Department/Cigna PBM Invoices/' . $sortedLocation;
exec('php -f email.php ' . $email . ' ' . $fileLocation);
