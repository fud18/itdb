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
  });

</SCRIPT>

<link rel="stylesheet" type="text/css" href="../../css/datatable.css">
<link rel="stylesheet" type="text/css" href="../../css/editablegrid.css">
<link rel="stylesheet" type="text/css" href="../../css/itdb.css">
<link rel="stylesheet" type="text/css" href="../../css/jquery.jqplot.css">
<link rel="stylesheet" type="text/css" href="../../css/jquery.tag.css">
<link rel="stylesheet" type="text/css" href="../../css/jquery.tag.list.css">
<link rel="stylesheet" type="text/css" href="../../css/sweetTitles.css">
<link rel="stylesheet" type="text/css" href="../../css/TableTools_JUI.css">
<link rel="stylesheet" type="text/css" href="../../css/theme.css">

<?php
/* Cory Funk 2015, cfunk@fhsu.edu */

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$initok=1;
require("../../init.php");

//delete Jack
if (isset($_GET['delid'])) { //Deletes the record in the current row 
	$delid=$_GET['delid'];
	$sql="DELETE from jacks WHERE id=".$_GET['delid'];
	$sth=db_exec($dbh,$sql);
	echo "<script>document.location='$scriptname?action=listjacks'</script>";
	echo "<a href='$scriptname?action=listjacks'></a>"; 
	exit;
}

// Get jack information
$sql="SELECT jacks.*, locations.nameid FROM jacks JOIN locations WHERE locations.id = locationid";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $jacks[$r['id']]=$r;
$sth->closeCursor();

// Get Location information
$sql="SELECT * from locations order by name,floor";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $locations[$r['id']]=$r;
$sth->closeCursor();

// Get Area/Room information
$sql="SELECT * FROM locareas order by areaname";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $locareas[$r['id']]=$r;
$sth->closeCursor();

// Get Department information
$sql="SELECT * FROM departments order by division,name";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $departments[$r['id']]=$r;
$sth->closeCursor();

// Get VLAN information
$sql="SELECT * FROM vlans order by vlanid";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $vlans[$r['id']]=$r;
$sth->closeCursor();

//export: export to excel (as html table readable by excel)
if (isset($_GET['export']) && $_GET['export']==1) {
  header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
  header("Expires: Thu, 01 Dec 1994 16:00:00 GMT");
  header("Cache-Control: Must-Revalidate");
  header('Content-Disposition: attachment; filename=itdb.xls');
  header('Connection: close');
  $export=1;
  $expand=1;//always export expanded view
}
else 
  $export=0;


if (!$export) 
  $perpage=48;
else 
  $perpage=100000;

if ($page=="all") {
  $perpage=100000;
}



if ($export)  {
  echo "<html>\n<head><meta http-equiv=\"Content-Type\"".
     " content=\"text/html; charset=UTF-8\" /></head>\n<body>\n";
}



// Display list
if ($export) 
  echo "\n<table border='1'>\n";
else {
  echo "<h1>Hansen Scholarship Hall <a title='Add new jack' href='../../index.php?action=editjack&amp;id=new'>".
       "<img border=0 src='../../images/add.png'></a></h1>\n";
  echo "<form name='frm'>\n";
  echo "\n<table class='brdr'>\n";
}

if (!$export) {
  $get2=$_GET;
  unset($get2['orderby']);
  $url=http_build_query($get2);
}

if (!isset($orderby) && empty($orderby)) 
  $orderby="jacks.id asc";
elseif (isset($orderby)) {

  if (stristr($orderby,"FROM")||stristr($orderby,"WHERE")) {
    $orderby="id";
  }
  if (strstr($orderby,"DESC"))
    $ob="+ASC";
  else
    $ob="+DESC";
}

echo "<thead>\n";
$thead= "\n<tr>".
	 "<th><a href='$fscriptname?$url&amp;orderby=jacks.id$ob'>ID</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=userdev$ob'>User / Device</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=switchname$ob'>Switch Name</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=locationid$ob'>Building [Floor]</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=locareaid$ob'>Area/Room</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=wallcord$ob'>Wall</br>Location</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=jackname$ob'>Jack</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=departmentsid$ob'>Department</a></th>".
     "<th><a href='#'>Module & Port</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=vlanid$ob'>VLAN</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=vlanname$ob'>VLAN Name</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=notes$ob'>Notes</a></th>".
	 "<th><button type='submit'><img border=0 src='../../images/search.png'></button></th>";


if ($export) {
 //clean links from excel export
  $thead = preg_replace('@<a[^>]*>([^<]+)</a>@si', '\\1 ', $thead); 
  $thead = preg_replace('@<img[^>]*>@si', ' ', $thead); 
}

echo $thead;
echo "</tr>\n</thead>\n";
echo "\n<tbody>\n";
echo "\n<tr>";

//create pre-fill form box vars
$userdev=isset($_GET['userdev'])?$_GET['userdev']:"";
$switchname=isset($_GET['switchname'])?($_GET['switchname']):"";
$locationid=isset($_GET['locationid'])?($_GET['locationid']):"";
$locareaid=isset($_GET['locareaid'])?($_GET['locareaid']):"";
$wallcoord=isset($_GET['wallcoord'])?$_GET['wallcoord']:"";
$jackname=isset($_GET['jackname'])?($_GET['jackname']):"";
$departmentsid=isset($_GET['departmentsid'])?$_GET['departmentsid']:"";
$modport=isset($_GET['modport'])?($_GET['modport']):"";
$vlanid=isset($_GET['vlanid'])?$_GET['vlanid']:"";
$vlanname=isset($_GET['vlanname'])?$_GET['vlanname']:"";
$notes=isset($_GET['notes'])?$_GET['notes']:"";
$page=isset($_GET['page'])?$_GET['page']:"all";

// Display Search Boxes
if (!$export) {

  echo "\n<td></td>";
  echo "<td title='User or Device'><input style='width:25em' type=text value='$userdev' name='userdev' id='srchUserdev'></td>";
  echo "<td title='Switch Name'><input type=text value='$switchname' name='switchname' id='srchSwitchname'></td>";?>

<!-- Location Information -->
		<td><center><?php te("HSH");?></center></td>
<!-- end, Location Information -->

<!-- Room/Area Information -->
		<?php if (is_numeric($locationid))?>
		<td><center><select name='locareaid' id='srchLocareaid'>
			<option value=''><?php te("Select");?></option>
			<?php 
			foreach ($locareas  as $key=>$locarea ) {
				$dbid=$locarea['id']; 
				$itype=$locarea['areaname'];
				$s="";
				if (($locareaid=="$dbid")) $s=" SELECTED "; 
				echo "    <option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
            </center>
		</td>
<?php
	echo "<td title='(N)orth<br />(S)outh<br />(E)ast<br />(W)est<br />'><center><select style='width:13em' name='wallcoord' id='srchWallcoord'>
			<option value=''>All</option>
			<option value='N'>N</option>
			<option value='S'>S</option>
			<option value='E'>E</option>
			<option value='W'>W</option>
		  </select></center></td>";
  echo "<td title='1A-100-1b'><input style='width:auto' type=text value='$jackname' name='jackname' id='srchJackname'></td>";?>
		<td title='Department Name'><center>
			<select style='width:auto' name='departmentsid' id='archDepartmentsid'>
			<option value=''><?php te("Select");?></option>
			<?php 
			foreach ($departments as $key=>$department ) {
				$dbid=$department['id']; 
				$itype=$department['abbr'];
				$s="";
				if (($departmentsid=="$dbid")) $s=" SELECTED "; 
				echo "<option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
		</center></td>
		<td title='tg.1.50<br />ge.1.1<br />fe.1.1<br />e.0.1'><center><input style='width:7em' type=text value='<?php $modport?>' name='modport' id='srchModport'></center></td>
<?php
	echo "<td title='VLAN'>";?>
			<select style='width:auto' id='srchvlanid' name='vlanid'>
			<option value=''><?php te("Select");?></option>
			<?php 
			foreach ($vlans as $key=>$vlan ) {
				$dbid=$vlan['id']; 
				$itype=$vlan['vlanid'];
				$s="";
				if (($vlanid=="$dbid")) $s=" SELECTED "; 
				echo "<option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
		</td>
<?php
	echo "<td title='VLAN Name'>";?>
			<select style='width:auto' name='vlanname' id='srchVlanname'>
			<option value=''><?php te("Select");?></option>
			<?php 
			foreach ($vlans as $key=>$vlan ) {
				$dbid=$vlan['id']; 
				$itype=$vlan['vlanname'];
				$itype2=$vlan['vlanid'];
				$s="";
				if (($vlanid=="$dbid")) $s=" SELECTED "; 
				echo "<option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
		</td>
	<td title='Notes'><input style='width:30em' type=text value='<?php $notes?>' name='notes' id='srchNotes'></td>
	<td></td>
<?php  }
  echo "</tr>\n\n";

// Create WHERE clause
if (isset($userdev) && strlen($userdev)) $where.="AND userdev LIKE '%$userdev%' ";
if (isset($switchname) && strlen($switchname)) $where.="AND switchname LIKE '%$switchname%' ";
if (isset($locareaid) && strlen($locareaid)) $where.="AND (locareaid = '$locareaid') ";
if (isset($wallcoord) && strlen($wallcoord)) $where.="AND wallcoord LIKE '%$wallcoord%' ";
if (isset($jackname) && strlen($jackname)) $where.="AND jackname LIKE '%$jackname%' ";
if (isset($departmentsid) && strlen($departmentsid)) $where.="AND departmentsid LIKE '%$departmentsid%' ";
if (isset($modport) && strlen($modport)) $where.="AND modport LIKE '%$modport%' ";
if (isset($vlanid) && strlen($vlanid)) $where.="AND vlanid LIKE '%$vlanid%' ";
if (isset($vlanname) && strlen($vlanname)) $where.="AND vlanname LIKE '%$vlanname%' ";
if (isset($notes) && strlen($notes)) $where.="AND notes LIKE '%$notes%' ";

///////////////////////////////////////////////////////////							Pagination							///////////////////////////////////////////////////////////

//	How many records are in table
$sth=db_execute($dbh,"SELECT count(jacks.id) as totalrows, locations.nameid FROM jacks JOIN locations WHERE locations.id = locationid AND nameid = 58000 $where");
$totalrows=$sth->fetchColumn();

//	Page Links
//	Get's the current page number
$get2=$_GET;
unset($get2['page']);
$url=http_build_query($get2);

///////////////////////////////////////////////////////////							end, Pagination							///////////////////////////////////////////////////////////

//page links
$get2=$_GET;
unset($get2['page']);
$url=http_build_query($get2);

$t=time();
$sql="SELECT jacks.*, locations.nameid FROM jacks JOIN locations WHERE locations.id = locationid AND nameid = 58000 $where order by $orderby LIMIT $totalrows";
$sth=db_execute($dbh,$sql);

// Display Results
$currow=0;
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
$currow++;

// Table Row
  if ($currow%2) $c="class='dark'";
  else $c="";

// Username
  $user=isset($userlist[$r['userid']])?$userlist[$r['userid']]['username']:"";

  echo "\n<tr $c>".  	"<td><a class='editiditm icon edit' title='Edit' href='../../index.php?action=editjack&amp;id=".$r['id']."' target='_top'><span>Edit</span></a></td>"?>
<td style="width:25em"><?php echo $r['userdev']?></td>
	<td style="width:auto"><?php echo $r['switchname']?></td>
<!-- Location Information -->
		<td><center><?php echo $locations[$r['locationid']]['abbr']." [".$locations[$r['locationid']]['floor']."]"?></center></td>
<!-- end, Location Information -->

<!-- Room/Area Information -->
		<td><center><?php echo $locareas[$r['locareaid']]['areaname']?></center></td>
<!-- end, Room/Area Information -->

<!-- Wall Location -->
		<?php 
			if ($r['wallcoord']=="N") {$N="checked='checked'";$S="";$E="";$W="";}
			if ($r['wallcoord']=="S") {$S="checked='checked'";$N="";$E="";$W="";}
			if ($r['wallcoord']=="E") {$E="checked='checked'";$N="";$S="";$W="";}
			if ($r['wallcoord']=="W") {$W="checked='checked'";$N="";$S="";$E="";}
		
	echo "<td title='Select (N)orth, (S)outh, (E)ast, (W)est'>";
		echo "<input ".$N."class='radio' type=radio name='".$r['id']."wallcoord' value='N'>".te("N");
		echo "<input ".$S."class='radio' type=radio name='".$r['id']."wallcoord' value='S'>".te("S");
		echo "<input ".$E."class='radio' type=radio name='".$r['id']."wallcoord' value='E'>".te("E");
		echo "<input ".$W."class='radio' type=radio name='".$r['id']."wallcoord' value='W'>".te("W");
	echo"</td>";
        
        ?>
<!-- end, Wall Location -->

<td style='width:uato'><?php echo $r['jackname']?></td>
	<td style='width:auto'><center><?php echo $departments[$r['departmentsid']]['abbr']?></center></td>
	<td style='width:7em'><center><?php echo $r['modport']?></center></td>
<!-- VLAN ID Information -->
	<td style='width:auto'><center><?php echo $vlans[$r['vlanid']]['vlanid']?></center></td>
<!-- end, VLAN ID Information -->

<!-- VLAN Name Information -->
	<td style='width:auto'><center><?php echo $vlans[$r['vlanid']]['vlanname']?></center></td>
<!-- end, VLAN Name Information -->

	<td style='width:20em'><?php echo $r['notes']?></td>
	<td><center><input type='image' src='../../images/delete.png' onclick='javascript:delconfirm2(\"{$r['id']}\",\"../../index.php?action=$action&amp;delid={$r['id']}\");'></center>
		<input type=hidden name='action' value='$action'>
		<input type=hidden name='id' value='$id'></td>

<?php	}
  echo "</td></tr>";

$sth->closeCursor();

if ($export) {
  echo "</tbody>\n</table>\n";
  exit;
}
else {
    $cs=13;

?>
  <tr><td colspan='<?php echo $cs?>' class=tdc></td></tr>
  </tbody>
  </table>
  <input type='hidden' name='action' value='<?php echo $_GET['action']?>'>
  </form>

<?php  ///////////////////////////////////////////////////////////							Pagination Links							///////////////////////////////////////////////////////////?>

<div class='gray'>
  <br /><b><center><?php echo $totalrows?> results</center><br>
    
<?php  ///////////////////////////////////////////////////////////							end, Pagination	Links						///////////////////////////////////////////////////////////?>

<?php 
}

if ($export) {
  echo "\n</body>\n</html>\n";
  exit;
}

?>