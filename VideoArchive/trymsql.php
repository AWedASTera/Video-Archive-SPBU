<?php
/*$mysqlHost = 'localhost';
$mysqlUser = 'root';
$mysqlPass = 'rootdb';
$mysqlDatabase = 'videoArchiveEncoder';

$link=mysqli_connect($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDatabase)
		or die("Bad ".mysqli_error());
$queue="select ip,port,path,id_disk from disks";
$result_disks=mysqli_query($link,$queue) or die("Error");
for ($i=0;$i<mysqli_num_rows($result_disks);++$i){
	$row_disks=mysqli_fetch_row($result_disks);
	$ftp=ftp_connect($row_disks[0], 21);
	
	if (!$ftp) echo "Error2";
	$username="ftpuser";
	$passwd="user";
	
	$login=ftp_login($ftp,$username,$passwd);
	if (!$login) echo "Error1";
	ftp_chdir($ftp, $row_disks[2]);
	$videos=ftp_nlist($ftp,".");
	$r=$row_disks[0];
	$queue="select videos_from_disks.name from videos_from_disks,disks where disks.id_disk=videos_from_disks.id_disk and disks.ip='$r'";
	$result_videos=mysqli_query($link,$queue) or die("Error3");
	$count_videos=mysqli_num_rows($result_videos);
	if ($count_videos==0)
		foreach ($videos as $video){
			$queue="insert into videos_from_disks (name,id_disk) values ('$video', '$row_disks[3]')";
			$result_add=mysqli_query($link,$queue) or die("Error4");
		}
	else{
		$videos_mysql=[];
		for ($j=0;$j<$count_videos;$j++){
			$videos_row=mysqli_fetch_row($result_videos);
			$videos_mysql[$j]=$videos_row[0];
		}
		foreach ($videos as $video)
			if (!in_array($video, $videos_mysql)){
				$queue="insert into videos_from_disks (name,id_disk) values ('$video', '$row_disks[3]')";
				$result_add=mysqli_query($link,$queue) or die("Error5");
			}
	}
}
$queue="select ip from disks";
$result=mysqli_query($link,$queue);
$arr=array();
for ($i=0;$i<mysqli_num_rows($result);$i++)
{
	$arr[$i]=mysqli_fetch_row($result)[0];
	echo "$arr[$i]";
}*/
?>
<?php
$mysqlHost = 'localhost';
$mysqlUser = 'root';
$mysqlPass = 'rootdb';
$mysqlDatabase = 'videoArchiveEncoder';
//if (isset($_POST['IP']) && isset($_POST['Port']) && isset($_POST['Path'])){
//if (!empty($_POST['IP']) && !empty($_POST['Port']) && !empty($_POST['Path'])){
	$ip="1";
	$port="1";
	$path="1";
	$link=mysqli_connect($mysqlHost,$mysqlUser,$mysqlPass,$mysqlDatabase) or die("Error" . mysqli_error());
	$queue="insert into disks (ip,port,path) values('$ip','$port','$path')";
	$result=mysqli_query($link,$queue);
	$arr=array();
	if (!$result)
	{
		$arr["success"]= 0;
		echo "not cool";
	}else
	{
		$arr["success"]= 1;
		$arr["ip"]="$ip";
		//echo json_encode($arr);
		echo "cool";
	}
/*}
else
	echo json_encode(array("success" => 0));*/
?>