<?php
require_once dirname(__FILE__) . '/../videos/configuration.php';
//if (isset($_POST['IP']) && isset($_POST['Port']) && isset($_POST['Path'])){
if (!empty($_POST['IP']) && !empty($_POST['Port']) && !empty($_POST['Path'])){
	$ip=strip_tags($_POST['IP']);
	$port=strip_tags($_POST['Port']);
	$path=strip_tags($_POST['Path']);
	$link=mysqli_connect($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDatabase) or die("Error" . mysqli_error());
	$queue="insert into disks (ip,port,path) values('$ip','$port','$path')";
	$result=mysqli_query($link,$queue);
	if (!$result)
		echo json_encode(array("success" => 0));
	else
		echo json_encode(array("success" => 1));
}
else
	echo json_encode(array("success" => 0));
?>
