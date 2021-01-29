<h1>BPO Sorter Test</h1>

<?php

$conn = oci_connect('cw', 'todoall', 'NO1_CWP1.CWIBENEFITS.COM');

$procedure = "BEGIN v3p_cigna_pbm_sort(); END;";

// $stid = oci_parse($conn, 'SELECT * FROM v3t_cigna_sort');

$stmt = oci_parse($conn, $procedure);
$stid = oci_parse($conn, 'SELECT * FROM v3t_cigna_sort');

oci_execute($stmt);
oci_execute($stid);


$looup = [];

while ($row = oci_fetch_object($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	// var_dump($row);
	// die('dump row');

	// $lookup['node'] = $row->NODE;
	// $lookup[$row->NODE]['cigna_group_id'] = $row->CIGNA_GROUP_ID;
	// $lookup[$row->NODE]['group_name'] = $row->GROUP_NAME;
	// $lookup[$row->NODE]['group_id'] = $row->GROUP_ID;

	$lookup[$row->CIGNA_GROUP_ID] = ['node'=> $row->NODE, 'group' => $row->GROUP_NAME];
}

var_dump($lookup);
die("Look up");