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
			/*1*/	'type',
			/*2*/	'title',
			/*3*/	'contactadd',
			/*4*/	'contactadd2',
			/*5*/	'contactcity',
			/*6*/	'contactstate',
			/*7*/	'contactzip',
			/*8*/	'urls',
			/*9*/	'salescontactname',
			/*10*/	'salescontactphone',
			/*11*/	'salescontactcellphone',
			/*12*/	'salescontactfax',
			/*13*/	'salescontactemail',
			/*14*/	'salescontacturl',
			/*15*/	'salescontactnotes',
			/*16*/	'supportcontactname',
			/*17*/	'supportcontactphone',
			/*18*/	'supportcontactcellphone',
			/*19*/	'supportcontactfax',
			/*20*/	'supportcontactemail',
			/*21*/	'supportcontacturl',
			/*22*/	'supportcontactnotes',
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

		$sqlTable = "CREATE TABLE IF NOT EXISTS agents(id INTEGER PRIMARY KEY AUTOINCREMENT,title VARCHAR,type VARCHAR,contactadd VARCHAR,contactadd2 VARCHAR,contactcity VARCHAR,contactstate VARCHAR,contactzip VARCHAR,urls VARCHAR,salescontactname VARCHAR,salescontactphone VARCHAR,salescontactcellphone VARCHAR,salescontactfax VARCHAR,salescontactemail VARCHAR,salescontacturl VARCHAR,salescontactnotes VARCHAR,supportcontactname VARCHAR,supportcontactphone VARCHAR,supportcontactcellphone VARCHAR,supportcontactfax VARCHAR,supportcontactemail VARCHAR,supportcontacturl VARCHAR,supportcontactnotes VARCHAR)";
		
		if(!$dbh->query($sqlTable)){
			echo "Table creation failed: (" . $dbh->errno . ") " . $dbh->error;
		}
?>

<center>
<p>
The expected format is a CSV file.  Please download the template:
<br />
<br />
<a href='../data/files/agents_import_template.csv'><img src='../images/xcel2.jpg' height=25 border=0></a> <a href='../data/files/agents_import_template.csv'>Template</a>
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
        echo "<th>ID</th><th>Type</th><th>Title</th><th>Contact Add</th><th>Contact Add 2</th><th>Contact City</th><th>Contact State</th><th>Contact Zip</th><th>URLs</th><th>Sales Contact Name</th><th>Sales Contact Phone</th><th>Sales Contact Cell Phone</th><th>Sales Contact Fax</th><th>Sales Contact Email</th><th>Sales Contact URL</th><th>Sales Contact Notes</th><th>Support Contact Name</th><th>Support Contact Phone</th><th>Support Contact Cell Phone</th><th>Support Contact Fax</th><th>Support Contact Email</th><th>Support Contact URL</th><th>Support Contact Notes</th>";
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

	//Add Agents
	foreach ($imlines as $line_num => $agents) {
		if ($line_num==0 && $_POST['skip1st']) {
			continue;
		}

		if (!lineok($agents,$delim))
			continue;

		$cols=explode($delim,$agents);

		$id=$cols[$name2fno['id']];
		$type=$cols[$name2fno['type']];
		$title=$cols[$name2fno['title']];
		$contactadd=$cols[$name2fno['contactadd']];
		$contactadd2=$cols[$name2fno['contactadd2']];
		$contactcity=$cols[$name2fno['contactcity']];
		$contactstate=$cols[$name2fno['contactstate']];
		$contactzip=$cols[$name2fno['contactzip']];
		$urls=$cols[$name2fno['urls']];
		$salescontactname=$cols[$name2fno['salescontactname']];
		$salescontactphone=$cols[$name2fno['salescontactphone']];
		$salescontactcellphone=$cols[$name2fno['salescontactcellphone']];
		$salescontactfax=$cols[$name2fno['salescontactfax']];
		$salescontactemail=$cols[$name2fno['salescontactemail']];
		$salescontacturl=$cols[$name2fno['salescontacturl']];
		$salescontactnotes=$cols[$name2fno['salescontactnotes']];
		$supportcontactname=$cols[$name2fno['supportcontactname']];
		$supportcontactphone=$cols[$name2fno['supportcontactphone']];
		$supportcontactcellphone=$cols[$name2fno['supportcontactcellphone']];
		$supportcontactfax=$cols[$name2fno['supportcontactfax']];
		$supportcontactemail=$cols[$name2fno['supportcontactemail']];
		$supportcontacturl=$cols[$name2fno['supportcontacturl']];
		$supportcontactnotes=$cols[$name2fno['supportcontactnotes']];

	$sql=	"INSERT INTO agents ".
			"(id,type,title,contactadd,contactadd2,contactcity,contactstate,contactzip,urls,salescontactname,salescontactphone,salescontactcellphone,salescontactfax,salescontactemail,salescontacturl,salescontactnotes,supportcontactname,supportcontactphone,supportcontactcellphone,supportcontactfax,supportcontactemail,supportcontacturl,supportcontactnotes) ".
			" VALUES ".
			"(:id,:type,:title,:contactadd,:contactadd2,:contactcity,:contactstate,:contactzip,:urls,:salescontactname,:salescontactphone,:salescontactcellphone,:salescontactfax,:salescontactemail,:salescontacturl,:salescontactnotes,:supportcontactname,:supportcontactphone,:supportcontactcellphone,:supportcontactfax,:supportcontactemail,:supportcontacturl,:supportcontactnotes)";
			
        $stmt=db_execute2($dbh,$sql,
            array(
				'id'=>$id,
				'type'=>$type,
				'title'=>$title,
				'contactadd'=>$contactadd,
				'contactadd2'=>$contactadd2,
				'contactcity'=>$contactcity,
				'contactstate'=>$contactstate,
				'contactzip'=>$contactzip,
				'urls'=>$urls,
				'salescontactname'=>$salescontactname,
				'salescontactphone'=>$salescontactphone,
				'salescontactcellphone'=>$salescontactcellphone,
				'salescontactfax'=>$salescontactfax,
				'salescontactemail'=>$salescontactemail,
				'salescontacturl'=>$salescontacturl,
				'salescontactnotes'=>$salescontactnotes,
				'supportcontactname'=>$supportcontactname,
				'supportcontactphone'=>$supportcontactphone,
				'supportcontactcellphone'=>$supportcontactcellphone,
				'supportcontactfax'=>$supportcontactfax,
				'supportcontactemail'=>$supportcontactemail,
				'supportcontacturl'=>$supportcontacturl,
				'supportcontactnotes'=>$supportcontactnotes,
            )
        );
		 //echo "<br>Isql=$sql<br>";
	}

		echo "<center><br><h2>Agents Data Imported Successfully</h2><br></center>";	
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