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
			/*0*/    'id',
			/*1*/    'fibertype',
			/*2*/    'intra_inter',
			/*3*/    'light_guide',
			/*4*/    'fiberstrnd',
			/*5*/    'from_locationid',
			/*6*/    'from_locareaid',
			/*7*/    'from_jumper_no',
			/*8*/    'from_dev',
			/*9*/    'to_locationid',
			/*10*/   'to_locareaid',
			/*11*/   'to_jumper_no',
			/*12*/   'to_dev',
			/*13*/   'notes',
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

		$sqlTable = "CREATE TABLE IF NOT EXISTS fiber(id INTEGER PRIMARY KEY AUTOINCREMENT,fibertype VARCHAR,intra_inter VARCHAR,light_guide VARCHAR,fiberstrnd NUMERIC,from_locationid NUMERIC,from_locareaid NUMERIC,from_jumper_no VARCHAR,from_dev VARCHAR,to_locationid NUMERIC,to_locareaid NUMERIC,to_jumper_no VARCHAR, to_dev VARCHAR, notes VARCHAR)";
		
		if(!$dbh->query($sqlTable)){
			echo "Table creation failed: (" . $dbh->errno . ") " . $dbh->error;
		}
?>

<center>
<p>
The expected format is a CSV file.  Please download the template:
<br />
<br />
<a href='../data/files/fiber_import_template.csv'><img src='../images/xcel2.jpg' height=25 border=0></a> <a href='../data/files/fiber_import_template.csv'>Template</a>
<br />
<br />
</p>
</center>

<div style='width:100%'> <!-- import1 -->

<?php if ($nextstep==0) { ?>
<table border='0' class=tbl1 >
    <form method='post' name='importfrm' action='<?php echo $scriptname?>?action=<?php echo $action?>' enctype='multipart/form-data'>
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
    <input type=hidden name="nextstep" value="1">
    <input type=hidden name="imfn" value='<?php echo $imfn?>'>
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
        echo "<th>ID</th><th>Fiber Type</th><th>Intra/Inter Building</th><th>Light Guide</th><th>Fiber Strand #</th><th>From Location ID</th><th>From Locarea ID</th><th style='width:2em'>From Jumper #</th><th>From Device</th><th>To Location ID</th><th>To Locarea ID</th><th>To Jumper #</th><th>To Device</th><th>Notes</th>";
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

	//Add Fiber Records
	foreach ($imlines as $line_num => $fiber) {
		if ($line_num==0 && $_POST['skip1st']) {
			continue;
		}

		if (!lineok($fiber,$delim))
			continue;

		$cols=explode($delim,$fiber);

		$id=$cols[$name2fno['id']];
		$fibertype=$cols[$name2fno['fibertype']];
		$intra_inter=$cols[$name2fno['intra_inter']];
		$light_guide=$cols[$name2fno['light_guide']];
		$fiberstrnd=$cols[$name2fno['fiberstrnd']];
		$from_locationid=$cols[$name2fno['from_locationid']];
		$from_locareaid=$cols[$name2fno['from_locareaid']];
		$from_jumper_no=$cols[$name2fno['from_jumper_no']];
		$from_dev=$cols[$name2fno['from_dev']];
		$to_locationid=$cols[$name2fno['to_locationid']];
		$to_locareaid=$cols[$name2fno['to_locareaid']];
		$to_jumper_no=$cols[$name2fno['to_jumper_no']];
		$to_dev=$cols[$name2fno['to_dev']];
		$notes=$cols[$name2fno['notes']];

	$sql=	"INSERT INTO fiber ".
			"(id,fibertype,intra_inter,light_guide,fiberstrnd,from_locationid,from_locareaid,from_jumper_no,from_dev,to_locationid,to_locareaid,to_jumper_no,to_dev,notes) ".
			" VALUES ".
			"(:id,:fibertype,:intra_inter,:light_guide,:fiberstrnd,:from_locationid,:from_locareaid,:from_jumper_no,:from_dev,:to_locationid,:to_locareaid,:to_jumper_no,:to_dev,:notes)";
			
        $stmt=db_execute2($dbh,$sql,
            array(
				'id'=>$id,
				'fibertype'=>$fibertype,
				'intra_inter'=>$intra_inter,
				'light_guide'=>$light_guide,
				'fiberstrnd'=>$fiberstrnd,
				'from_locationid'=>$from_locationid,
				'from_locareaid'=>$from_locareaid,
				'from_jumper_no'=>$from_jumper_no,
				'from_dev'=>$from_dev,
				'to_locationid'=>$to_locationid,
				'to_locareaid'=>$to_locareaid,
				'to_jumper_no'=>$to_jumper_no,
				'to_dev'=>$to_dev,
				'notes'=>$notes,
            )
        );
		 //echo "<br>Isql=$sql<br>";
	}
		echo "<center><br><h2>Fiber Data Imported Successfully</h2><br></center>";	
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