<?php

// var_dump($_SERVER);
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
 //  var_dump($_SERVER);
	// var_dump($_FILES);
  // var_dump($_POST);

  $location = str_replace("I:\\", "", $_POST['location']);
  $location = str_replace("\\", "/", $location);

  if (is_uploaded_file($_FILES['my_upload']['tmp_name'])) 
  { 
  	//First, Validate the file name
  	if(empty($_FILES['my_upload']['name']))
  	{
  		$_SESSION['upload_status'] = " File name is empty! ";
  		exit;
  	}
 
  	$upload_file_name = $_FILES['my_upload']['name'];
  	//Too long file name?
  	if(strlen ($upload_file_name)>100)
  	{
  		$_SESSION['upload_status'] = " too long file name ";
  		exit;
  	}
 
  	//replace any non-alpha-numeric cracters in th file name
  	$upload_file_name = preg_replace("/[^A-Za-z0-9 \.\-_]/", '', $upload_file_name);
 
  	//set a limit to the file upload size
  	if ($_FILES['my_upload']['size'] > 1000000) 
  	{
		$_SESSION['upload_status'] = " too big file ";
  		exit;        
    }
 
    //Save the file
    $dest=__DIR__.'/drop/'.$upload_file_name;

    // var_dump($dest);
    if (move_uploaded_file($_FILES['my_upload']['tmp_name'], $dest)) 
    {
      $_SESSION['upload_status'] = 'File Has Been Uploaded !';
      $_SESSION['email']         = $_POST['email'];
      $_SESSION['location']      = $location;
      
    	// header("Location:http://ddb.cwibenefits.com/bpo/success.php");
      include 'success.php';
    } else {
      die('is_uploaded_file else');
    }
  }
} else {
  die('Request Method Else');
}