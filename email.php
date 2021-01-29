<?php
	$headers = 'From: DoNotReply@' . " \r\n";
	// $headers .= 'CC: josh.brown@cwibenefits.com' . " \r\n";
    $headers .= 'Content-type: text/html' . "\r\n";
    $headers .= 'X-Priority: 3' . "\r\n";
    $headers .= 'X-Mailer: PHP' . phpversion() . " \r\n";

    $to = '<' . $argv[1] . '>';
    $subject = 'Cigna PBM Invoice Sorter';
    $msg = '<html>';

    $msg .= '<p>The Cigna PBM Invoices have been completed.</p>';

    $msg .= '<p>Your files are located on the I: Drive at: ' . $argv[2] . '</p>';

    $msg .= '</html>';

    if (mail($to, $subject, $msg, $headers)) {
    	echo '<h3>Success!</h3>';
    } else {
    	echo '<h3>Failure</h3>';
    }

?>
