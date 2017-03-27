<?php
/* Cory Funk 2015, cfunk@fhsu.edu */

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$initok=1;
require("../init.php");

if (!isset($_POST['nextstep']))
	$nextstep=0;
else
	$nextstep=$_POST['nextstep'];


if (!isset($_POST['imfn']))
	$imfn="";
else
	$imfn=$_POST['imfn'];


/* csv field number to name mapping */
        $fno2name=array(
			/*0*/	'id',
			/*1*/	'itemtypeid',
			/*2*/	'function',
			/*3*/	'manufacturerid',
			/*4*/	'model',
			/*5*/	'sn',
			/*6*/	'sn2',
			/*7*/	'sn3',
			/*8*/	'origin',
			/*9*/	'warrantymonths',
			/*10*/	'purchasedate',
			/*11*/	'purchprice',
			/*12*/	'dnsname',
			/*13*/	'maintenanceinfo',
			/*14*/	'comments',
			/*15*/	'ispart',
			/*16*/	'hd',
			/*17*/	'cpu',
			/*18*/	'ram',
			/*19*/	'locationid',
			/*20*/	'userid',
			/*21*/	'ipv4',
			/*22*/	'ipv6',
			/*23*/	'usize',
			/*24*/	'rackmountable',
			/*25*/	'macs',
			/*26*/	'remadmip',
			/*27*/	'panelport',
			/*28*/	'ports',
			/*29*/	'switchport',
			/*30*/	'switchid',
			/*31*/	'rackid',
			/*32*/	'rackposition',
			/*33*/	'label',
			/*34*/	'status',
			/*35*/	'cpuno',
			/*36*/	'corespercpu',
			/*37*/	'rackposdepth',
			/*38*/	'warrinfo',
			/*39*/	'locareaid',
			/*40*/	'asset',
			/*41*/	'departmentsid',
			/*42*/	'departmentabbrsid',
			/*43*/	'vlanid',
			/*44*/	'vlanname',
);

$name2fno=array_flip($fno2name);
$nfields=count($fno2name);

//nextstep:
//0: show import form
//1: import file and if not successfull go to 0 else show imported file and fields, and candidate db objects
//2: DB insert

//echo "<p>NEXT1=$nextstep<br>";

if ($nextstep==1 && strlen($_FILES['file']['name'])>2) { //insert file
  $filefn=strtolower("import-".$_COOKIE["itdbuser"]."-".validfn($_FILES['file']['name']));
  $uploadedfile = "/tmp/".$filefn;
  $result = '';

  //Move the file from the stored location to the new location
  if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadedfile)) {
	  $result = "Cannot upload the file '".$_FILES['file']['name']."'"; 
	  if(!file_exists($uploaddir)) {
		  $result .= " : Folder doesn't exist.";
	  } elseif(!is_writable($uploaddir)) {
		  $result .= " : Folder not writable.";
	  } elseif(!is_writable($uploadedfile)) {
		  $result .= " : File not writable.";
	  }
	  $filefn = '';

	  echo "<br><b>ERROR: $result</b><br>";
	  $imfn="";
	  $nextstep=0;
  }
  else { //file ok
	  $nextstep=1;
	  //print "<br>Uploaded  $uploadedfile<br>";
	  $imfn=$uploadedfile;
	}
}//insert file

		$sqlTable = "CREATE TABLE IF NOT EXISTS items(id INTEGER PRIMARY KEY AUTOINCREMENT,itemtypeid VARCHAR,function VARCHAR,manufacturerid VARCHAR,model VARCHAR,sn VARCHAR,sn2 VARCHAR,sn3 VARCHAR,origin VARCHAR,warrantymonths VARCHAR,purchasedate VARCHAR,purchprice VARCHAR,dnsname VARCHAR,maintenanceinfo VARCHAR,comments VARCHAR,ispart VARCHAR,hd VARCHAR,cpu VARCHAR,ram VARCHAR,locationid VARCHAR,userid VARCHAR,ipv4 VARCHAR,ipv6 VARCHAR,usize VARCHAR,rackmountable VARCHAR,macs VARCHAR,remadmip VARCHAR,panelport VARCHAR,ports VARCHAR,switchport VARCHAR,switchid VARCHAR,rackid VARCHAR,rackposition VARCHAR,label VARCHAR,status VARCHAR,cpuno VARCHAR,corespercpu VARCHAR,rackposdepth VARCHAR,warrinfo VARCHAR,locareaid VARCHAR,asset VARCHAR,departmentsid VARCHAR,departmentabbrsid VARCHAR,vlanid VARCHAR,vlanname VARCHAR)";
		
		if(!$dbh->query($sqlTable)){
			echo "Table creation failed: (" . $dbh->errno . ") " . $dbh->error;
		}
?>

<center>
<p>
The expected format is a CSV file.  Please download the template:
<br />
<br />
<a href='../data/files/items_import_template.csv'><img src='../images/xcel2.jpg' height=25 border=0></a> <a href='../data/files/items_import_template.csv'>Template</a>
<br />
<br />
</p>
</center>

<div style='width:100%'> <!-- import1 -->

<?php if ($nextstep==0) { ?>
<table border='0' class=tbl1 >
    <form method=post name='importfrm' action='<?php echo $scriptname?>?action=<?php echo $action?>' enctype='multipart/form-data'>
    <tr><td>File:</td><td> <input name="file" id="file" size="100" type="file"></td></tr>
    <tr><td>Delimeter:</td><td><select id='delim' name='delim' maxlength=1>
            <option value='|'><?php te("|");?></option>
            <option value=','><?php te(",");?></option>
            <option value=';'><?php te(";");?></option>
            </select>
        </td>
    </tr>
    <tr><td>Skip 1st row:</td><td><select name=skip1st>
        <option value=1>Yes</option>
        <option value=0>No</option></select></td></tr>
    <tr><td colspan=2><input type="submit" value="Preview"></td></tr>
    <input type=hidden name='nextstep' value='1'>
    <input type=hidden name='imfn' value='<?php echo $imfn?>'>
    </form>
<?php }?>

<?php if ($nextstep==1) { 
	$delim=$_POST['delim'];
	$imlines=file($imfn);
?>

	<div style='height:95%;overflow:auto'>
	<table border='0'>
	<thead>
    <tr>
    <?php
        echo "<th>ID</th><th>Item Type ID</th><th>Function</th><th>Manufacturer ID</th><th>Model</th><th>SN</th><th>SN2</th><th>SN3</th><th>Origin</th><th>Warranty Months</th><th>Purchase Date</th><th>Purchase Price</th><th>DNS Name</th><th>Maintenance Info</th><th>Comments</th><th>Is Part</th><th>HD</th><th>CPU</th><th>RAM</th><th>Location ID</th><th>User ID</th><th>Ipv4</th><th>Ipv6</th><th>U Size</th><th>Rack Mountable</th><th>MAC</th><th>Remote Admin IP</th><th>Panel Port</th><th>Ports</th><th>Switch Port</th><th>Switch ID</th><th>Rack ID</th><th>Rack Position</th><th>Label</th><th>Status</th><th>CPU No.</th><th>Cores Per CPU</th><th>Rack Position Depth</th><th>Warranty Info</th><th>Locarea ID</th><th>Asset</th><th>Departments ID</th><th>Department Abbrs ID</th><th>VLAN ID</th><th>VLAN Name</th>";
   ?>
    </tr>
	</thead>
	<tbody>

	<?php
	foreach ($imlines as $line_num => $line) {
		if ($line_num==0 && $_POST['skip1st']) 
			continue;

		$cols=explode($delim,$line);
		echo "<tr>";
		foreach ($cols as $col) {
			$col=trim($col);
			echo "<td><center>$col</center></td>";
		}
		echo "</tr>\n";
	}

	echo "</tbody></table>\n";
	echo "</div>";
	?>

	<div style='text-align:center'>
    <center><table>
		<form method=post name='importfrm' action='<?php echo $scriptname?>?action=<?php echo $action?>' enctype='multipart/form-data'>
		<input type=hidden name='nextstep' value='0'>
		<tr><td><input type=submit value='Cancel' ></td>
		</form>
        
		<?php if ($nextstep!=0) { ?>
		<form method=post name='importfrm' action='<?php echo $scriptname?>?action=<?php echo $action?>' enctype='multipart/form-data'>
		<input type=hidden name='nextstep' value='2'>
		<td><input type=submit value='Import' ></td></tr>
		<input type=hidden name='delim' value='<?php echo $_POST['delim']?>'>
		<input type=hidden name='imfn' value='<?php echo $imfn?>'>
		<input type=hidden name='skip1st' value='<?php echo $_POST['skip1st']?>'>
		</form>
		<?php } ?>
	</table></center>
	</div>

<?php
}

if ($nextstep==2) {
	$imlines=file($imfn);

	foreach ($imlines as $line_num => $line) {
		if ($line_num==0 && $_POST['skip1st']) 
			continue;
	}

	// Add items Records
	foreach ($imlines as $line_num => $items) {
		if ($line_num==0 && $_POST['skip1st']) {
			continue;
		}

		$cols=explode($delim,$items);

		$id=$cols[$name2fno['id']];
		$itemtypeid=$cols[$name2fno['itemtypeid']];
		$function=$cols[$name2fno['function']];
		$manufacturerid=$cols[$name2fno['manufacturerid']];
		$model=$cols[$name2fno['model']];
		$sn=$cols[$name2fno['sn']];
		$sn2=$cols[$name2fno['sn2']];
		$sn3=$cols[$name2fno['sn3']];
		$origin=$cols[$name2fno['origin']];
		$warrantymonths=$cols[$name2fno['warrantymonths']];
		$purchasedate=$cols[$name2fno['purchasedate']];
		$purchprice=$cols[$name2fno['purchprice']];
		$dnsname=$cols[$name2fno['dnsname']];
		$maintenanceinfo=$cols[$name2fno['maintenanceinfo']];
		$comments=$cols[$name2fno['comments']];
		$ispart=$cols[$name2fno['ispart']];
		$hd=$cols[$name2fno['hd']];
		$cpu=$cols[$name2fno['cpu']];
		$ram=$cols[$name2fno['ram']];
		$locationid=$cols[$name2fno['locationid']];
		$userid=$cols[$name2fno['userid']];
		$ipv4=$cols[$name2fno['ipv4']];
		$ipv6=$cols[$name2fno['ipv6']];
		$usize=$cols[$name2fno['usize']];
		$rackmountable=$cols[$name2fno['rackmountable']];
		$macs=$cols[$name2fno['macs']];
		$remadmip=$cols[$name2fno['remadmip']];
		$panelport=$cols[$name2fno['panelport']];
		$ports=$cols[$name2fno['ports']];
		$switchport=$cols[$name2fno['switchport']];
		$switchid=$cols[$name2fno['switchid']];
		$rackid=$cols[$name2fno['rackid']];
		$rackposition=$cols[$name2fno['rackposition']];
		$label=$cols[$name2fno['label']];
		$status=$cols[$name2fno['status']];
		$cpuno=$cols[$name2fno['cpuno']];
		$corespercpu=$cols[$name2fno['corespercpu']];
		$rackposdepth=$cols[$name2fno['rackposdepth']];
		$warrinfo=$cols[$name2fno['warrinfo']];
		$locareaid=$cols[$name2fno['locareaid']];
		$asset=$cols[$name2fno['asset']];
		$departmentsid=$cols[$name2fno['departmentsid']];
		$departmentabbrsid=$cols[$name2fno['departmentabbrsid']];
		$vlanid=$cols[$name2fno['vlanid']];
		$vlanname=$cols[$name2fno['vlanname']];

	$sql=	"INSERT into items ".
			"(id,itemtypeid,function,manufacturerid,model,sn,sn2,sn3,origin,warrantymonths,purchasedate,purchprice,dnsname,maintenanceinfo,comments,ispart,hd,cpu,ram,locationid,userid,ipv4,ipv6,usize,rackmountable,macs,remadmip,panelport,ports,switchport,switchid,rackid,rackposition,label,status,cpuno,corespercpu,rackposdepth,warrinfo,locareaid,asset,departmentsid,departmentabbrsid,vlanid,vlanname) ".
			" VALUES ".
			"(:id,:itemtypeid,:function,:manufacturerid,:model,:sn,:sn2,:sn3,:origin,:warrantymonths,:purchasedate,:purchprice,:dnsname,:maintenanceinfo,:comments,:ispart,:hd,:cpu,:ram,:locationid,:userid,:ipv4,:ipv6,:usize,:rackmountable,:macs,:remadmip,:panelport,:ports,:switchport,:switchid,:rackid,:rackposition,:label,:status,:cpuno,:corespercpu,:rackposdepth,:warrinfo,:locareaid,:asset,:departmentsid,:departmentabbrsid,:vlanid,:vlanname)";
			
        $stmt=db_execute2($dbh,$sql,
            array(
				'id'=>$id,
				'itemtypeid'=>$itemtypeid,
				'function'=>$function,
				'manufacturerid'=>$manufacturerid,
				'model'=>$model,
				'sn'=>$sn,
				'sn2'=>$sn2,
				'sn3'=>$sn3,
				'origin'=>$origin,
				'warrantymonths'=>$warrantymonths,
				'purchasedate'=>$purchasedate,
				'purchprice'=>$purchprice,
				'dnsname'=>$dnsname,
				'maintenanceinfo'=>$maintenanceinfo,
				'comments'=>$comments,
				'ispart'=>$ispart,
				'hd'=>$hd,
				'cpu'=>$cpu,
				'ram'=>$ram,
				'locationid'=>$locationid,
				'userid'=>$userid,
				'ipv4'=>$ipv4,
				'ipv6'=>$ipv6,
				'usize'=>$usize,
				'rackmountable'=>$rackmountable,
				'macs'=>$macs,
				'remadmip'=>$remadmip,
				'panelport'=>$panelport,
				'ports'=>$ports,
				'switchport'=>$switchport,
				'switchid'=>$switchid,
				'rackid'=>$rackid,
				'rackposition'=>$rackposition,
				'label'=>$label,
				'status'=>$status,
				'cpuno'=>$cpuno,
				'corespercpu'=>$corespercpu,
				'rackposdepth'=>$rackposdepth,
				'warrinfo'=>$warrinfo,
				'locareaid'=>$locareaid,
				'asset'=>$asset,
				'departmentsid'=>$departmentsid,
				'departmentabbrsid'=>$departmentabbrsid,
				'vlanid'=>$vlanid,
				'vlanname'=>$vlanname,
            )
        );
		 //echo "<br>Isql=$sql<br>";
	}

		echo "<center><br><h2>Items Data Imported Successfully</h2><br></center>";	
}


function lineok ($line,$delim) {
    global $fno2name,$name2fno;

	$cols=explode($delim,$line);

	return 1;
}

function array_iunique($array) {
    if(!is_array($array))
        return null;
    elseif (!count($array))
        return array();
    else
    return array_intersect_key($array,array_unique(array_map(strtolower,$array)));
}



//echo "<p>NEXT2=$nextstep";
?>


</div> <!-- import1 -->