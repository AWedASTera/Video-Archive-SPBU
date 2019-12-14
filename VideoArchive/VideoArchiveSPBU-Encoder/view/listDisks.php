<?php
require_once __DIR__ . '/../videos/configuration.php';
$link=mysqli_connect($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDatabase);
$queue="select ip from disks";
$result=mysqli_query($link,$queue);
$arr=array();
for ($i=0;$i<mysqli_num_rows($result);$i++)
{
	$arr[$i]=mysqli_fetch_row($result)[0];
}
echo json_encode($arr);
?>
