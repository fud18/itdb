<?php /* Cory Funk 2015, cafunk@fhsu.edu */?>

<SCRIPT LANGUAGE="JavaScript"> 

  function confirm_filled($row)
  {
	  var filled = 0;
	  $row.find('input,select').each(function() {
		  if (jQuery(this).val()) filled++;
	  });
	  if (filled) return confirm('Do you really want to remove this row?');
	  return true;
  };

 $(document).ready(function() {

    //delete table row on image click
    $('.delrow').click(function(){
        var answer = confirm("Are you sure you want to delete this row ?")
        if (answer) 
	  $(this).parent().parent().remove();
    });
  });
</SCRIPT>

<?php 
if (!isset($initok)) {echo "do not run this script directly";exit;}

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// get department
$sql="SELECT * from departments WHERE id = '' OR id != '' order by id";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $departmentslist[$r['id']]=$r;
$sth->closeCursor();

$sql="SELECT * from users order by username";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $userlist[$r['id']]=$r;
$sth->closeCursor();

//delete department
if (isset($_GET['delid'])) { //if we came from a post (save) the update department 
  $delid=$_GET['delid'];
  

  //delete entry
  $sql="DELETE from department where id=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  echo "<script>document.location='$scriptname?action=listdepartment'</script>";
  echo "<a href='$scriptname?action=listdepartment'></a>"; 
  exit;

}


if (isset($_POST['id'])) { //if we came from a post (save) then update department 
  $id=$_POST['id'];

  if ($_POST['id']=="new")  {//if we came from a post (save) then add department 
    $sql="INSERT INTO departments (division, name, abbr) VALUES ('$division', '$name', '$abbr')";
	
/*$departmenttype, $intra_inter, $light_guide, $departmentstrnd, $from_locationid, $from_locareaid, $from_jumper_no, $from_dev, $to_locationid, $to_locareaid, $to_dev, $notes*/
		  
    db_exec($dbh,$sql,0,0,$lastid);
    $lastid=$dbh->lastInsertId();
    print "<br><b>Added Department <a href='$scriptname?action=$action&amp;id=$lastid'>$lastid</a></b><br>";
    echo "<script>window.location='$scriptname?action=$action&id=$lastid'</script> "; //go to the new item
    $id=$lastid;
  }
  else {
    $sql="UPDATE departments SET division='$division',name='$name',abbr='$abbr' WHERE id=$id";
    db_exec($dbh,$sql);
	
  echo "<script>document.location='$fscriptname?action=editdepartment&id=$id'</script>";
  echo "<a href='$fscriptname?action=editdepartment&id=$id'></a>"; 
  exit;
  }


}//save pressed

if ($id!="new") {
  //get current item data
  $id=$_GET['id'];
  $sql="SELECT * FROM departments WHERE id='$id'";
  $sth=db_execute($dbh,$sql);
  $dept=$sth->fetchAll(PDO::FETCH_ASSOC);
  
	//  Next & Previous Buttons' Function
	$curid = intval($dept[0]);

    // Select contents from the selected id
    $sql = "SELECT * FROM departments WHERE id='$curid'";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $info = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die('Not found');
    }

    // Next Record
    $sql = "SELECT id FROM departments WHERE id>'$id' LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $nextresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$nextid = strval($nextresults[0]['id']);
    }

    // Previous Record
    $sql = "SELECT id FROM departments WHERE id<'$id' ORDER BY id DESC LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $prevresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$previd = strval($prevresults[0]['id']);
    }
} else {
    // No form has been submitted so use the lowest id and grab its info
    $sql = "SELECT * FROM departments WHERE id > 0 LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $inforesults = $result->fetchAll(PDO::FETCH_ASSOC);
		$info =  strval($inforesults[0]['id']);
		
    }
}

///////////////////////////////// display data now


if (!isset($_REQUEST['id'])) {echo "ERROR:ID not defined";exit;}
$id=$_REQUEST['id'];

$sql="SELECT * FROM departments WHERE id='$id'";
$sth=db_execute($dbh,$sql);
$r=$sth->fetch(PDO::FETCH_ASSOC);
$ph="";

if ($id !="new")
$id=$r['id'];

echo "\n<form method=post  action='$scriptname?action=$action&amp;id=$id' enctype='multipart/form-data'  name='addfrm'>\n";

if ($id=="new")
  echo "\n<h1>".t("Add Department")."</h1>\n";
else
  echo "\n<h1>".t("Edit Department")."</h1>\n";

?>
<table border="0" cellpadding="5" cellspacing="5" class="tbl1">

<!-- Department Properties Title -->
    <tr> 
      <td class='tdtop'>
        <table border='0' class="tbl2">
          
<!-- Department Properties Title -->
	  <tr>
		<td class='tdt'><?php te("Division");?>:</td>
		<td><center><select name="division" id="division" style="width:35em">
			<option value='<?php echo $r['division']?>'><?php echo $r['division']?></option>
			<option value=''></option>
                <?php
                $sql="SELECT DISTINCT departments.division FROM departments";
                $sth=$dbh->query($sql);
                $departments=$sth->fetchAll(PDO::FETCH_ASSOC);
                foreach ($departments as $d) {
                    $dbid=$d['id'];
                    $itype=$d['division'];
                    $s="";
                    if (isset($_GET['division']) && $_GET['division']=="$itype") $s=" SELECTED ";
                    echo "<option $s value='".$itype."' title='$itype'>$itype</option>\n";
                }
                echo "</select></center>
		</td>
      </tr>
      <tr>
          <td class='tdt'>"?><?php te("Department Name");?>:</td>
          <td><input style="width:33em" id='name' name='name' value='<?php echo $r['name'] ?>'></input></td>
      </tr>
      <tr>
          <td class='tdt'><?php te("Department Abbr.");?>:</td>
          <td><input style="width:33em" id='name' name='abbr' value='<?php echo $r['abbr'] ?>'></input></td>
      </tr>
<!-- end, department Properties Title -->
</table>

<table border="0" class="tbl2" width="100%" align="center">
          <tr>
			<td>
				<?php if ($previd != "") { ?>
                    <a href='?action=editdepartment&amp;id=<?php echo $previd?>'><button type="button"><img title='Previous Record' src='images/prev_rec.png' border=0><?php echo t("&nbsp; Previous Record")?></button></a>
                <?php } else {?>
                    <a href='#'><button type="button"><img title='Previous Record' src='images/prev_rec.png' border=0><?php echo t("&nbsp; Previous Record")?></button></a>
                <?php }?>
            </td>
            <td><button type="submit"><img src="images/save.png" alt="Save" /><?php te(" Save");?></button></td>
            <?php echo "\n<td><button type='button' onclick='javascript:delconfirm2(\"{$r['id']}\",\"$scriptname?action=$action&amp;delid={$r['id']}\");'>"."<img title='Delete' src='images/delete.png' border=0>".t(" Delete")."
		</button></td>";
		echo "\n<input type=hidden name='action' value='$action'>";
		echo "\n<input type=hidden name='id' value='$id'>";
		?>
        <td>
<?php if ($nextid != "") { ?>
<a href='?action=editdepartment&amp;id=<?php echo $nextid?>'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
<?php } else {?>
	<a href='#'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
<?php }?>
</td>
</tr>
</table>

    </form>
</body>
</html>