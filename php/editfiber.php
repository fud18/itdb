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
</SCRIPT>
<?php 
if (!isset($initok)) {echo "do not run this script directly";exit;}

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// get fiber
$sql="SELECT * from fiber WHERE id = '' OR id != '' order by id";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $fiberlist[$r['id']]=$r;
$sth->closeCursor();

$sql="SELECT * from users order by username";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $userlist[$r['id']]=$r;
$sth->closeCursor();

$sql="SELECT * from locations order by name,floor";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $locations[$r['id']]=$r;
$sth->closeCursor();

$sql="SELECT * from locareas order by areaname";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $locareas[$r['id']]=$r;
$sth->closeCursor();

//delete fiber
if (isset($_GET['delid'])) { //if we came from a post (save) the update fiber 
  $delid=$_GET['delid'];
  

  //delete entry
  $sql="DELETE from fiber where id=".$_GET['delid'];
  $sth=db_exec($dbh,$sql);

  echo "<script>document.location='$scriptname?action=listfiber'</script>";
  echo "<a href='$scriptname?action=listfiber'></a>"; 
  exit;

}


if (isset($_POST['id'])) { //if we came from a post (save) then update fiber 
  $id=$_POST['id'];

  if ($_POST['id']=="new")  {//if we came from a post (save) then add fiber 
    $sql="INSERT INTO fiber (fibertype, intra_inter, light_guide, fiberstrnd, from_locationid, from_locareaid, from_jumper_no, from_dev, to_locationid, to_locareaid, to_dev, notes) VALUES ('$fibertype', '$intra_inter', '$light_guide', '$fiberstrnd', '$from_locationid', '$from_locareaid', '$from_jumper_no', '$from_dev', '$to_locationid', '$to_locareaid', '$to_dev', '$notes')";
	
/*$fibertype, $intra_inter, $light_guide, $fiberstrnd, $from_locationid, $from_locareaid, $from_jumper_no, $from_dev, $to_locationid, $to_locareaid, $to_dev, $notes*/
		  
    db_exec($dbh,$sql,0,0,$lastid);
    $lastid=$dbh->lastInsertId();
    print "<br><b>Added Fiber <a href='$scriptname?action=$action&amp;id=$lastid'>$lastid</a></b><br>";
    echo "<script>window.location='$scriptname?action=$action&id=$lastid'</script> "; //go to the new item
    $id=$lastid;
  }
  else {
    $sql="UPDATE fiber SET ".
       " fibertype='$fibertype', intra_inter='$intra_inter',light_guide='$light_guide',fiberstrnd='$fiberstrnd',from_locationid='$from_locationid',from_locareaid='$from_locareaid',from_jumper_no='$from_jumper_no',from_dev='$from_dev',to_locationid='$to_locationid',to_locareaid='$to_locareaid',to_dev='$to_dev',notes='$notes' WHERE id=$id";
    db_exec($dbh,$sql);
	
  echo "<script>document.location='$fscriptname?action=editfiber&id=$id'</script>";
  echo "<a href='$fscriptname?action=editfiber&id=$id'></a>"; 
  exit;
  }


}//save pressed

if ($id!="new") {
  //get current item data
  $id=$_GET['id'];
  $sql="SELECT * FROM fiber WHERE id='$id'";
  $sth=db_execute($dbh,$sql);
  $dept=$sth->fetchAll(PDO::FETCH_ASSOC);
  
	//  Next & Previous Buttons' Function
	$curid = intval($dept[0]);

    // Select contents from the selected id
    $sql = "SELECT * FROM fiber WHERE id='$curid'";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $info = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die('Not found');
    }

    // Next Record
    $sql = "SELECT id FROM fiber WHERE id>'$id' LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $nextresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$nextid = strval($nextresults[0]['id']);
    }

    // Previous Record
    $sql = "SELECT id FROM fiber WHERE id<'$id' ORDER BY id DESC LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $prevresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$previd = strval($prevresults[0]['id']);
    }
} else {
    // No form has been submitted so use the lowest id and grab its info
    $sql = "SELECT * FROM fiber WHERE id > 0 LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $inforesults = $result->fetchAll(PDO::FETCH_ASSOC);
		$info =  strval($inforesults[0]['id']);
		
    }
}

///////////////////////////////// display data now


if (!isset($_REQUEST['id'])) {echo "ERROR:ID not defined";exit;}
$id=$_REQUEST['id'];

$sql="SELECT * FROM fiber WHERE id='$id'";
$sth=db_execute($dbh,$sql);
$r=$sth->fetch(PDO::FETCH_ASSOC);

if ($id !="new")
$fiberid=$r['id'];

echo "\n<form method=post  action='$scriptname?action=$action&amp;id=$id' enctype='multipart/form-data'  name='addfrm'>\n";

if ($id=="new")
  echo "\n<h1>".t("Add Fiber")."</h1>\n";
else
  echo "\n<h1>".t("Edit Fiber $id")."</h1>\n";

?>
<!-- Fiber Properties -->
	<table border='0' class=tbl1 width="100%">
	<tr>

<!-- General Fiber Info -->
		<td class='tdtop'>
			<table border='0' class=tbl2>
				<tr><td colspan=2><h3><?php te("General Fiber Information");?></h3></td></tr>
<!-- Fiber Type -->
	<tr>
		<td class='tdt'><?php te("Fiber Type");?>:</td>
		<td><select style='width:20em' id='fibertype' name='fibertype'>
			<option value='<?php echo $r['fibertype']?>'><?php echo $r['fibertype']?></option>
			<option value=''></option>
			<option value='MultiMode'>MultiMode</option>
			<option value='SingleMode'>SingleMode</option>
			</select>
		</td>
	</tr>
<!-- end, Fiber Type -->

	  <tr>
		<td class='tdt'><?php te("Intra/Inter Building");?>:</td>
      	<td title='IntraBuilding or InterBuilding Connectivity' ><select style='width:20em' name='intra_inter'>
  		<option value='<?php echo $r['intra_inter']?>'><?php echo $r['intra_inter']?></option>
  		<option value=''></option>
  		<option value='Intra-Building'>Intra-Building</option>
  		<option value='Inter-Building'>Inter-Building</option>
        </select>
        </td>
      </tr>
	  <tr>
		<td class='tdt'><?php te("Light Guide");?>:</td>
        <td><select style='width:20em' id='light_guide' name='light_guide'>
			<option value='<?php echo $r['light_guide']?>'><?php echo $r['light_guide']?></option>
			<?php 
			foreach ($fiber as $key=>$f ) {
				$dbid=$f['id']; 
				$itype=$f['light_guide'];
				$s="";
				if (($id=="$dbid")) $s=" SELECTED "; 
				echo "    <option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
		</td>
      </tr>
	  <tr>
		<td class='tdt'><?php te("Fiber Strand Number");?>:</td>
        <td title='Fiber Strand Number'><select style='width:20em' id='fiberstrnd' name='fiberstrnd'>
				<option value='<?php echo $r['fiberstrnd']?>'><?php echo $r['fiberstrnd']?></option>
				<option value=''></option>
				<?php
					for( $i= 1 ; $i < 289 ; $i++ )
					{
						echo '<option ' . ($i == 0 ? 'selected=\'selected\'' : '') . ' value="' . $i . '" >' . $i . '</option>';
					}
                ?></select></td>
		</tr>
        
	  <tr>
		<td class='tdt'><?php te("Notes");?>:</td>
		<td title='Notes'><textarea style='width:19.5em' wrap='soft' class=tarea1  id='notes' name='notes'><?php echo $r['notes']?></textarea></td>
      </tr>
	</table>

<!-- From Information -->
<!-- General Fiber Info -->
		<td class='tdtop'>
			<table border='0' class=tbl2>
				<tr><td colspan=2><h3><?php te("From");?></h3></td></tr>

	  <tr>
		<td class='tdt' width="auto"><?php te("Building");?>:</td>
		<td ><select id='from_locationid' name='from_locationid' style="width:20em">
			<option value='<?php echo $r['from_locationid']?>'><?php echo $locations[$r['from_locationid']]['name']?></option>
			<option value=''></option>
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
<!-- end, Location Information -->


<!-- Room/Area Information -->
	  <tr>
		<td class='tdt'><?php te("Area/Room");?>:</td>
		<?php if (is_numeric($from_locationid))?>
		<td><center><select id='from_locareaid' name='from_locareaid' style="width:20em">
			<option value='<?php echo $r['from_locareaid']?>'><?php echo $locareas[$r['from_locareaid']]['areaname']?></option>
			<option value=''></option>
			<?php 
			foreach ($locareas  as $key=>$locarea ) {
				$dbid=$locarea['id']; 
				$itype=$locarea['areaname'];
				$s="";
				if (($locareaid=="$dbid")) $s=" SELECTED "; 
				echo "    <option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select></center>
		</td>
      </tr>
	  <tr>
		<td class='tdt'><?php te("Jumper Number");?>:</td>
		<td title='Jumper Number (From)'><center><select id='from_jumper_no' name='from_jumper_no' style="width:20em">
				<option value='<?php echo $r['from_jumper_no']?>'><?php echo $r['from_jumper_no']?></option>
				<option value=''><?php te("");?></option>
				<?php
					for( $i= 1000 ; $i < 2501 ; $i++ )
					{
						echo '<option ' . ($i == 0 ? 'selected=\'selected\'' : '') . ' value="' . $i . '" >' . $i . '</option>';
					}
                ?></select></center>
                </td>
       </tr>

	  <tr>
		<td class='tdt'><?php te("Device");?>:</td>
		<td title='From Device'><input type=text name='from_dev' id='from_dev' value='<?php echo $r['from_dev']?>' style="width:20em"></td>
      </tr>
	</table>
    
<!-- To Information -->
		<td class='tdtop'>
			<table border='0' class=tbl2>
				<tr><td colspan=2><h3><?php te("To");?></h3></td></tr>
	  <tr>
		<td class='tdt' width="auto"><?php te("Building");?>:</td>
		<td><select id='to_locationid' name='to_locationid' style="width:20em">
			<option value='<?php echo $r['to_locationid']?>'><?php echo $locations[$r['to_locationid']]['name']?></option>
			<option value=''></option>
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
<!-- end, Location Information -->


<!-- Room/Area Information -->
	  <tr>
		<td class='tdt'><?php te("Area/Room");?>:</td>
		<?php if (is_numeric($to_locationid))?>
		<td><center><select id='to_locationid' name='to_locationid' style="width:20em">
			<option value='<?php echo $r['to_locationid']?>'><?php echo $locareas[$r['to_locationid']]['areaname']?></option>
			<option value=''></option>
			<?php 
			foreach ($locareas  as $key=>$locarea ) {
				$dbid=$locarea['id']; 
				$itype=$locarea['areaname'];
				$s="";
				if (($locareaid=="$dbid")) $s=" SELECTED "; 
				echo "    <option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select></center>
		</td>
      </tr>

	  <tr>
		<td class='tdt'><?php te("Jumper Number");?>:</td>
		<td title='Jumper Number (From)'><center><select id='to_jumper_no' name='to_jumper_no' style="width:20em">
				<option value='<?php echo $r['to_jumper_no']?>'><?php echo $r['to_jumper_no']?></option>
				<option value=''><?php te("");?></option>
				<?php
					for( $i= 1000 ; $i < 2501 ; $i++ )
					{
						echo '<option ' . ($i == 0 ? 'selected=\'selected\'' : '') . ' value="' . $i . '" >' . $i . '</option>';
					}
                ?></select></center>
                </td>
       </tr>

	  <tr>
		<td class='tdt'><?php te("Device");?>:</td>
		<td title='From Device'><input type=text name='to_dev' id='to_dev' value='<?php echo $r['to_dev']?>' style="width:20em"></td>
      </tr>
      </table>
	</td>
</tr>
            
<!-- end, fiber Properties Title -->
</table>

<table border="0" class="tbl2" width="100%" align="center">
          <tr>
			<td>
				<?php if ($previd != "") { ?>
                    <a href='?action=editfiber&amp;id=<?php echo $previd?>'><button type="button"><img title='Previous Record' src='images/prev_rec.png' border=0><?php echo t("&nbsp; Previous Record")?></button></a>
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
<a href='?action=editfiber&amp;id=<?php echo $nextid?>'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
<?php } else {?>
	<a href='#'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
<?php }?>
</td>
</tr>
</table>

    </form>
</body>
</html>