<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Delete Record</title>
</head>

<body>
<?php 

if (!isset($initok)) {echo "do not run this script directly";exit;}

/* Cory Funk 2018, cafunk_at_ scatcat.fhsu.edu */

$sql="DELETE FROM vlans WHERE id='$_GET[id]'";
if($sth=db_execute($dbh,$sql))
	header("refresh:1; url=listvlans.php");
else
	echo "Record Not Deleted!"
?>
</body>
</html>