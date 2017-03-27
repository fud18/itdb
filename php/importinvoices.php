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
			/*1*/	'number',
			/*2*/	'date',
			/*3*/	'vendorid',
			/*4*/	'buyerid',
			/*5*/	'description',
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

		$sqlTable = "CREATE TABLE IF NOT EXISTS agents(id INTEGER PRIMARY KEY AUTOINCREMENT,number VARCHAR,date VARCHAR,vendorid VARCHAR,buyerid VARCHAR,description VARCHAR)";
		
		if(!$dbh->query($sqlTable)){
			echo "Table creation failed: (" . $dbh->errno . ") " . $dbh->error;
		}
?>

<center>
<p>
The expected format is a CSV file.  Please download the template:
<br />
<br />
<a href='../data/files/invoices_import_template.csv'><img src='../images/xcel2.jpg' height=25 border=0></a> <a href='../data/files/invoices_import_template.csv'>Template</a>
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
        echo "<th>ID</th><th>Number</th><th>Date</th><th>Vendor ID</th><th>Buyer ID</th><th>description</th>";
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

	// Add invoices Records
	foreach ($imlines as $line_num => $invoices) {
		if ($line_num==0 && $_POST['skip1st']) {
			echo "<br>Skipped the first line (header)<br>";
			echo "<br><h2>invoices Data Imported Successfully</h2><br>";	
			continue;
		}

		$cols=explode($delim,$invoices);

		$id=$cols[$name2fno['id']];
		$number=$cols[$name2fno['number']];
		$date=$cols[$name2fno['date']];
		$vendorid=$cols[$name2fno['vendorid']];
		$buyerid=$cols[$name2fno['buyerid']];
		$description=$cols[$name2fno['description']];

	$sql=	"INSERT into invoices ".
			"(id,number,date,vendorid,buyerid,description) ".
			" VALUES ".
			"(:id,:number,:date,:vendorid,:buyerid,:description)";
			
        $stmt=db_execute2($dbh,$sql,
            array(
				'id'=>$id,
				'number'=>$number,
				'date'=>$date,
				'vendorid'=>$vendorid,
				'buyerid'=>$buyerid,
				'description'=>$description,
            )
        );
		 //echo "<br>Isql=$sql<br>";
	}

		echo "<center><br><h2>Invoices Data Imported Successfully</h2><br></center>";	
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