<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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

    $("#caddrow").click(function($e) {
	var row = $('#contactstable tr:last').clone(true);
        $e.preventDefault();
	row.find("input:text").val("");
	row.find("img").css("display","inline");
	row.insertAfter('#contactstable tr:last');
    });
    $("#uaddrow").click(function($e) {
	var row = $('#urlstable tr:last').clone(true);
        $e.preventDefault();
	row.find("input:text").val("");
	row.find("img").css("display","inline");
	row.insertAfter('#urlstable tr:last');
    });
  });

  $(document).ready(function() {
    $("#locationid").change(function() {
      var locationid=$(this).val();
      var locareaid=$('#locareaid').val();
      var dataString = 'locationid='+ locationid;
	  
      $.ajax ({
	  type: "POST",
	  url: "php/locarea_options_ajax.php",
	  data: dataString,
	  cache: false,
	  success: function(html) {
	    $("#locareaid").html(html);
	  }
      });
    });
	
	  $("#departmentsid").change(function() {
      var departmentsid=$(this).val();
      var departmentabbrid=$('#departmentabbrsid').val();
      var dataString = 'departmentsid='+ departmentsid;
	  
      $.ajax ({
	  type: "POST",
	  url: "php/dept_options_ajax.php",
	  data: dataString,
	  cache: false,
	  success: function(html) {
	    $("#departmentabbrsid").html(html);
	  }
      });
    });
  });

//remove invalid filename characters


</SCRIPT>
<script type="text/javascript" src="../js/ckeditor/ckeditor.js"></script>
<?php 

if (!isset($initok)) {echo "do not run this script directly";exit;}

/* Spiros Ioannou 2009-2010 , sivann _at_ gmail.com */
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$sql="SELECT * FROM users order by upper(username)";
$sth=$dbh->query($sql);
$userlist=$sth->fetchAll(PDO::FETCH_ASSOC);

$sql="SELECT * FROM locations order by name";
$sth=$dbh->query($sql);
$locations=$sth->fetchAll(PDO::FETCH_ASSOC);

//delete Project
if (isset($_GET['delid'])) { //if we came from a post (save) the update project 
  $delid=$_GET['delid'];
  

  //delete entry
  $sql="DELETE from projects where id=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  echo "<script>document.location='$scriptname?action=listprojects'</script>";
  echo "<a href='$scriptname?action=listprojects'>Go here</a></body></html>"; 
  exit;

}
$projectname=htmlentities($projectname, ENT_QUOTES);
$proj_submitter=htmlentities($proj_submitter, ENT_QUOTES);
$proj_status=htmlentities($proj_status, ENT_QUOTES);
$locationid=htmlentities($locationid, ENT_QUOTES);
$locareaid=htmlentities($locareaid, ENT_QUOTES);
$summary=htmlentities($summary, ENT_QUOTES);
$notes=htmlentities($notes, ENT_QUOTES);

if (isset($_POST['id'])) { //if we came from a post (save) then update project 
  $id=$_POST['id'];

if ($_POST['id']=="new")  {//if we came from a post (save) then add project 
	$sql="INSERT INTO projects (projectname, proj_submitter, proj_status, locationid, locareaid, summary, notes) VALUES ('$projectname', '$proj_submitter', '$proj_status', '$locationid', '$locareaid', '$summary', '$notes')";
    db_exec($dbh,$sql,0,0,$lastid);
    $lastid=$dbh->lastInsertId();
    print "<br><b>Added project <a href='$scriptname?action=$action&amp;id=$lastid'>$lastid</a></b><br>";
    echo "<script>window.location='$scriptname?action=$action&id=$lastid'</script> "; //go to the new item
							
    $id=$lastid;
  }
  else {
    $sql="UPDATE projects SET projectname='$projectname', proj_submitter='$proj_submitter', proj_status='$proj_status',locationid='$locationid',locareaid='$locareaid',summary='$summary', notes='$notes' WHERE id=$id";
    db_exec($dbh,$sql);
  }


}//save pressed
if ($id!="new") {
  //get current item data
  $id=$_GET['id'];
  $sql="SELECT * FROM projects WHERE id='$id'";
  $sth=db_execute($dbh,$sql);
  $proj=$sth->fetchAll(PDO::FETCH_ASSOC);
  
	//  Next & Previous Buttons' Function
	$curid = intval($proj[0]);

    // Select contents from the selected id
    $sql = "SELECT * FROM projects WHERE id='$curid'";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $info = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die('Not found');
    }

    // Next Record
    $sql = "SELECT id FROM projects WHERE id>'$id' LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $nextresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$nextid = strval($nextresults[0]['id']);
    }

    // Previous Record
    $sql = "SELECT id FROM projects WHERE id<'$id' ORDER BY id DESC LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $prevresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$previd = strval($prevresults[0]['id']);
    }
} else {
    // No form has been submitted so use the lowest id and grab its info
    $sql = "SELECT * FROM projects WHERE id > 0 LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $inforesults = $result->fetchAll(PDO::FETCH_ASSOC);
		$info =  strval($inforesults[0]['id']);
		
    }
}

///////////////////////////////// display data 

//if (!isset($_REQUEST['id'])) {echo "ERROR:ID not defined";exit;}
//$id=$_REQUEST['id'];

$sql="SELECT * FROM projects WHERE id='$id'";
$sth=db_execute($dbh,$sql);
$r=$sth->fetch(PDO::FETCH_ASSOC);

if ($id !="new")
$projectname=$r['projectname'];$proj_submitter=$r['proj_submitter'];$proj_status=$r['proj_status'];$locationid=$r['locationid'];$locareaid=$r['locareaid'];$summary=$r['summary'];$notes=$r['notes'];

echo "\n<form method=post action='$scriptname?action=$action&amp;id=$id' enctype='multipart/form-data'  name='addfrm'>\n";

if ($id=="new")
  echo "\n<h1>".t("Add Project")."</h1>\n";
else
  echo "\n<h1>".t("Edit Project $id")."</h1><left>
"/*    	  <p align='left' style='color:#DF0101'>NOTE: The use of single/double quotes will cause an error posting to the database if you must use these characters<br/>
  								                   please escape them by doubling them (e.g. ' = '')  **If you miss doing this just use your [Back] Button to fix the problem.</p>"; */
?>

<table border="0" cellpadding="5" cellspacing="5" class="tbl1">

<!-- Project Properties Title -->
    <tr> 
      <td class='tdtop'>
        <table border='0' class="tbl2">
          
<!-- Building Information -->
    <tr> 
      <td class='tdtop'>
          <tr>
            <td colspan=2><h3>
                <?php te("Project Information");?>
              </h3></td>
          </tr>

<!-- Project Properties Title -->
      <tr>
          <td class='tdt'><?php te("Project Name");?>:</td>
          <td><input style="width:33em" id='projectname' name='projectname' value='<?php echo $r['projectname']?>'></input></td>
      </tr>
<!-- end, Project Properties Title -->

<!-- Project Submitter -->
      <tr>
      <td class='tdt'><?php te("Project Submitter");?>:</td><td title='<?php te("User that submitted this item");?>'>
      <select id='proj_submitter' name='proj_submitter'>
      <option value=''><?php te("Select User");?></option>
      <?php 
		for ($i=0;$i<count($userlist);$i++) {
			$dbid=$userlist[$i]['id'];
			$itype=$userlist[$i]['userdesc'];
			$s="";
		if ($proj_submitter==$dbid) $s=" SELECTED ";
	//echo "<option $s value='$dbid'>".sprintf("%02d",$dbid)."-$itype</option>\n";
	echo "<option $s value='$dbid'>$itype</option>\n";
      }
      ?>

      </select>
      </td>
      </tr>
<!-- end, Project Submitter -->

<!-- Project Status -->
	<tr>
		<td class='tdt'><?php te("Project Status");?>:</td>
		<td title='<?php te("What is the current status of the project?");?>'><select style='width:16em' id='proj_status' name='proj_status' />
        		<option value=''><?php echo $r['proj_status']?></option>
                <option title='<?php te("Cost, Best Possible Method, Time/Time Constraints, etc...");?>' value='Planning'>Planning</option>
                <option title='<?php te("Steps towards completing the project.");?>' value='In Progress'>In Progress</option>
                <option title='<?php te("Final touches to complete project.");?>' value='Finalizing'>Finalizing</option>
                <option title='<?php te("Nothing more needed.");?>' value='Complete'>Complete</option>
			</select>
		</td>
	</tr>
<!-- end, Project Status -->

<!-- Building Information -->
    <tr> 
      <td class='tdtop'>
          <tr>
            <td colspan=2><h3>
                <?php te("Building Information");?>
              </h3></td>
          </tr>
          
<!-- Building Name & Floor -->
      <tr>
      <td class='tdt'>
		<?php echo "<a title='Add New Building' href='$scriptname?action=editlocation&id=new'><img src='images/add.png' alt='+'></a> ";
			  echo "<a alt='Edit' title='".t("Edit Building or Room")."' href='$scriptname?action=editlocation&id=$locationid'><img src='images/edit2.png'></a> ";?>
			  <?php te("Location");?>:</td>
      <td>
	<select style="width:33em" id='locationid' name='locationid'>
	<option value=''><?php te("Select");?></option>
	<?php 
	foreach ($locations  as $key=>$location ) {
	  $dbid=$location['id']; 
	  $itype=$location['name'];
	  $s="";
	  if (($locationid=="$dbid")) $s=" SELECTED "; 
	  echo "    <option $s value='$dbid'>$itype</option>\n";

	}
	?>
	</select>

      </td>
      </tr>
<!-- end, Building Name & Floor -->

<!-- Area/Room -->
      <tr>
      <?php 
      if (is_numeric($locationid)) {
	$sql="SELECT * FROM locareas WHERE locationid=$locationid order by areaname";
	$sth=$dbh->query($sql);
	$locareas=$sth->fetchAll(PDO::FETCH_ASSOC);
      } 
      else 
	$locareas=array();
      ?>
		<td class='tdt' class='tdt'><?php te("Area/Room");?>:</td>
		<td>
			<select style="width:33em" id='locareaid' name='locareaid'>
			<option value=''><?php te("Select");?></option>
			<?php 
			foreach ($locareas  as $key=>$locarea )
			{
			$dbid=$locarea['id']; 
			$itype=$locarea['areaname'];
			$s="";
			if (($locareaid=="$dbid")) $s=" SELECTED "; 
			echo "    <option $s value='$dbid'>$itype</option>\n";
			}
			?>
		</select>
		</td>
	</tr>
<!-- end, Area/Room -->
<!-- end, Building Information -->

<!-- Project Details -->
          <tr>
            <td colspan=2><h3>
                <?php te("Project Information");?>
              </h3></td>
          </tr>

<!-- Summary -->
          <tr>
            <td class="tdt2"><?php te("Brief Summary");?>:</td>
            <td><textarea wrap='soft' class='tarea1' style='height:200px;width:1024px' name='summary'><?php echo $summary?></textarea></td>
          </tr>
<!-- end, Summary -->

<!-- Notes -->
          <tr>
            <td class="tdt2"><?php te("Project Details");?>:</td>
            <td><textarea wrap='soft' class='tarea1' style='height:768px;width:1024px' id='notes' name='notes'><?php echo $notes?></textarea></td>
				<script>
					CKEDITOR.replace( 'notes' );
				</script>

          </tr>
        </table>
<!-- end, Notes -->
</table>
<table width="100%"><!-- save buttons -->
<tr>
<td>
<?php if ($previd != "") { ?>
	<a href='?action=editvlan&amp;id=<?php echo $previd?>'><button type="button"><img title='Previous Record' src='images/prev_rec.png' border=0><?php echo t("&nbsp; Previous Record")?></button></a>
<?php } else {?>
	<a href='#'><button type="button"><img title='Previous Record' src='images/prev_rec.png' border=0><?php echo t("&nbsp; Previous Record")?></button></a>
<?php }?>
</td>
<td style='text-align: center' colspan=1><button type="submit"><img src="images/save.png" alt="Save" > <?php te("Save");?></button></td>
<?php 
if ($id!="new") {
  echo "\n<td style='text-align: center' ><button type='button' onclick='javascript:delconfirm2(\"Item {$_GET['id']}\",\"$scriptname?action=$action&amp;delid={$_GET['id']}\");'>".
       "<img title='Delete' src='images/delete.png' border=0>".t("Delete")."</button></td>\n";

  echo "\n<td style='text-align: center' ><button type='button' onclick='javascript:cloneconfirm(\"Item {$_GET['id']}\",\"$scriptname?action=$action&amp;cloneid={$_GET['id']}\");'>".
       "<img  src='images/copy.png' border=0>". t("Clone")."</button></td>\n";
} 
else 
  echo "\n<td>&nbsp;</td>";
?>
<td style="text-align:right;">
<?php if ($nextid != "") { ?>
<a href='?action=editvlan&amp;id=<?php echo $nextid?>'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
<?php } else {?>
	<a href='#'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
<?php }?>
</td>
</tr>
</table>

<input type=hidden name='id' value='<?php echo $id ?>'>
<input type=hidden name='action' value='<?php echo $action ?>'>

			   
					 
		   
			
</form>

</body>
</html>