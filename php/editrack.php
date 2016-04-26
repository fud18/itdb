<SCRIPT LANGUAGE="JavaScript"> 

$(document).ready(function() {

  $("#tabs").tabs();
  $("#tabs").show();

    $("#locationid").change(function() {
      var locationid=$(this).val();
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
<?php 

if (!isset($initok)) {echo "do not run this script directly";exit;}

/* Spiros Ioannou 2009-2010 , sivann _at_ gmail.com */

$sql="SELECT * FROM locations order by name";
$sth=$dbh->query($sql);
$locations=$sth->fetchAll(PDO::FETCH_ASSOC);



//delete rack
if (isset($_GET['delid'])) { 
  $delid=$_GET['delid'];
  if (!is_numeric($delid)) {
    echo "Non numeric id delid=($delid)";
    exit;
  }

  //first handle rack associations
  $nitems=countitemsinrack($delid);
  if ($nitems>0) {
    echo "<b>Rack not deleted: Please remove items first from this rack<br></b>\n";
    echo "<br><a href='javascript:history.go(-1);'>Go back</a>\n</body></html>";
    exit;
  }
  else {
    delrack($delid,$dbh);
    echo "<script>document.location='$scriptname?action=listracks'</script>\n";
    echo "<a href='$scriptname?action=listracks'>Go here</a>\n</body></html>"; 
    exit;
  }

}

if (isset($_POST['id'])) { //if we came from a post (save), update the rack 
  $id=$_POST['id'];
  $title=$_POST['title'];
  $type=$_POST['type'];
  $date=ymd2sec($_POST['date']);


  //don't accept empty fields
  if ((empty($_POST['usize']))||  (empty($_POST['depth'])))  {
    echo "<br><b>Some <span class='mandatory'> mandatory</span> fields are missing.</b><br>".
         "<a href='javascript:history.go(-1);'>Go back</a></body></html>";
    exit;
  }


  if ($_POST['id']=="new")  {//if we came from a post (save) the add software 

    $sql="INSERT into racks (locationid , usize , depth , comments,model,label, revnums , locareaid) ".
	 " VALUES ('$locationid','$usize','$depth','$comments','$model','$label','$revnums','$locareaid')";
    db_exec($dbh,$sql,0,0,$lastid);
    $lastid=$dbh->lastInsertId();
    print "<br><b>Added Rack <a href='$scriptname?action=$action&amp;id=$lastid'>$lastid</a></b><br>";
    echo "<script>window.location='$scriptname?action=$action&id=$lastid'</script> "; //go to the new rack
    echo "\n</body></html>";
    $id=$lastid;
    exit;

  }//new rack
  else {
    $sql="UPDATE racks set ".
      " locationid='".$_POST['locationid']."', ".
      " locareaid='".$_POST['locareaid']."', ".
      " usize='".$_POST['usize']."', ".
      " revnums='".$_POST['revnums']."', ".
      " depth='".$_POST['depth']."', ".
      " model='".($_POST['model'])."', ".
      " comments='".($_POST['comments'])."' , ".
      " label='".($_POST['label'])."' ".
      " WHERE id=$id";

    db_exec($dbh,$sql);
  }//not new-update

  //update item locations to point to rack location
  $sql="UPDATE items set locationid='".$_POST['locationid']."', locareaid='".$_POST['locareaid']."' WHERE items.rackid=$id";
  db_exec($dbh,$sql);
  te("Location of items in this rack was updated to match rack location");

}//save pressed

if ($id!="new") {
  //get current item data
  $id=$_GET['id'];
  $sql="SELECT * FROM racks WHERE id='$id'";
  $sth=db_execute($dbh,$sql);
  $dept=$sth->fetchAll(PDO::FETCH_ASSOC);
  
	//  Next & Previous Buttons' Function
	$curid = intval($dept[0]);

    // Select contents from the selected id
    $sql = "SELECT * FROM racks WHERE id='$curid'";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $info = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die('Not found');
    }

    // Next Record
    $sql = "SELECT id FROM racks WHERE id>'$id' LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $nextresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$nextid = strval($nextresults[0]['id']);
    }

    // Previous Record
    $sql = "SELECT id FROM racks WHERE id<'$id' ORDER BY id DESC LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $prevresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$previd = strval($prevresults[0]['id']);
    }
} else {
    // No form has been submitted so use the lowest id and grab its info
    $sql = "SELECT * FROM racks WHERE id > 0 LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $inforesults = $result->fetchAll(PDO::FETCH_ASSOC);
		$info =  strval($inforesults[0]['id']);
		
    }
}

///////////////////////////////// display data 


if (!isset($_REQUEST['id'])) {echo "ERROR:ID not defined";exit;}
$id=$_REQUEST['id'];

//$sql="SELECT * FROM racks where racks.id='$id'";
$sql="SELECT count(items.id) AS population, sum(items.usize) as occupation,racks.* 
FROM racks 
LEFT OUTER JOIN items 
ON items.rackid=racks.id 
WHERE items.id IN 
(
	SELECT items.id
	FROM items  
	WHERE items.rackid ='$id'
	GROUP BY items.rackposition
)";
$sth=db_execute($dbh,$sql);
$r=$sth->fetch(PDO::FETCH_ASSOC);

if (($id !="new") && (count($r)<2)) {echo "ERROR: non-existent ID<br>($sql)";exit;}
echo "\n<form id='mainform' method=post  action='$scriptname?action=$action&amp;id=$id' enctype='multipart/form-data'  name='addfrm'>\n";

?>



<?php 
if ($id=="new")
  echo "\n<h1>".t("Add Rack")."</h1>\n";
else
  echo "\n<h1>".t("Edit Rack")."  ($id)"."</h1>\n";

?>

<!-- error errcontainer -->
<div class='errcontainer ui-state-error ui-corner-all' style='padding: 0 .7em;width:700px;margin-bottom:3px;'>
        <p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span>
        <h4><?php te("There are errors in your form submission, please see below for details");?>.</h4>
        <ol>
                <li><label for="usize" class="error"><?php te("Rack height is missing");?></label></li>
                <li><label for="depth" class="error"><?php te("Rack depth is missing");?></label></li>
                <li><label for="label" class="error"><?php te("Rack label is missing");?></label></li>
                <li><label for="locationid" class="error"><?php te("Rack location is missing");?></label></li>
        </ol>
</div>

<table style='width:100%' border=0>


<tr>
<td class="tdtop" width=20%>

    <table class="tbl2" style='width:300px;'>
    <tr><td colspan=2><h3>File Properties</h3></td></tr>
    <tr><td class="tdt">ID:</td> <td><input  style='display:none' type=text name='id' value='<?php echo $id?>' readonly size=3><?php echo $id?></td></tr>
    <tr><td class="tdt"><?php te("Height (U)")?></td><td>
    <select class='mandatory' validate='required:true' name='usize'>
<?php 
    echo "\n<option  value=''>".t("Select")."</option>";
    for ($s=50;$s>3;$s--) {
      if ($s==$r['usize']) $sel="selected"; else $sel="";
      echo "<option $sel value='$s'>".$s."U</option>\n";
    }
?>
    </select>
    </td></tr>

    <tr><td class="tdt"><?php te("Numbering")?></td><td>
    <select name='revnums'>
<?php
    if ($r['revnums']==1) {
      $s0="";$s1="selected";
    }
    else {
      $s0="selected"; $s1="";
    }
    echo "<option $s0 value='0'>1=Bottom</option>\n";
    echo "<option $s1 value='1'>1=Top</option>\n";
?>
    </select>
    </td>
    </tr>

    <tr><td class="tdt"><?php te("Label");?>:</td> 
        <td><input  class='input2 mandatory' validate='required:true' size=20 type=text name='label' value="<?php echo $r['label']?>"></td></tr>
    <tr><td class="tdt"><?php te("Depth");?>(mm):</td> 
        <td><input  class='input2 mandatory' validate='required:true' size=20 type=text name='depth' value="<?php echo $r['depth']?>"></td></tr>
    <tr><td class="tdt"><?php te("Model");?>:</td> 
        <td><input  class='input2 mandatory' size=20 type=text name='model' value="<?php echo $r['model']?>"></td></tr>
    <tr><td class="tdt"><?php te("Comments");?>:</td> 
        <td><textarea class='tarea1' wrap=soft name=comments><?php echo $r['comments']?></textarea></td></tr>
    <tr><td class="tdt">
		<?php echo "<a title='Add New Building' href='$scriptname?action=editlocation&id=new'><img src='images/add.png' alt='+'></a> ";
			  echo "<a alt='Edit' title='".t("Edit Building or Room")."' href='$scriptname?action=editlocation&id=$locationid'><img src='images/edit2.png'></a> ";?>
			  <?php te("Location");?>:</td> 

    <td>
      <select id='locationid' name='locationid' validate='required:true'>
      <option value=''>Select</option>
      <?php
      $locationid=$r['locationid'];
      foreach ($locations  as $key=>$location ) {
	$dbid=$location['id'];
	$itype=$location['name'].", Floor:".$location['floor'];
	$s="";
	if (($locationid=="$dbid")) $s=" SELECTED ";
	echo "    <option $s value='$dbid'>$itype</option>\n";
      }
      ?>
      </select>
    </td>
    </tr>

    <tr><td class="tdt"><?php te("Area");?>:</td><td>
<?php
    if (is_numeric($locationid)) {
      $sql="SELECT id,areaname FROM locareas WHERE locationid=$locationid order by areaname";
      $stha=$dbh->query($sql);
      $locareas=$stha->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
      $locareas=array();
    }
?>
      <select id='locareaid' name='locareaid'>
	<option value=''>Select</option>
	<?php
	$locareaid=$r['locareaid'];
	foreach ($locareas  as $key=>$locarea ) {
	  $dbid=$locarea['id'];
	  $name=$locarea['areaname'];
	  $s="";
	  if (($locareaid=="$dbid")) $s=" SELECTED ";
	  echo "    <option $s value='$dbid'>$name</option>\n";
	}
	?>
      </select>
    </td>
    </tr>
    <tr><td class="tdt"><?php te("Items");?>:</td> <td><?php echo $r['population']?></td>
    <tr>
       <?php $occupation=(int)$r['occupation'];
	     if ($id!="new")
	       $width=(int)($occupation/$r['usize']*100/(100/150));
		//$width=100;
	      else 
	        $width=0;
       ?>
       <td class='tdt'><?php te("Occupation");?></td>
       <td title='<?php echo $occupation?> U occupied'>
	 <div style='width:150px;border:1px solid #888;padding:0;'>
	 <div style='background-color:#EAAF0F;width:<?php echo $width?>px'>&nbsp;</div></div>
       </td>
    </tr>
    </table>
<?php

?>
</td>

<td class='smallrack' style='padding-left:10px;border-left:1px dashed #aaa'>
  <?php
  if ($id!="new")
    include('viewrack.php');
  ?>
  </td>
  </tr>
  </table>

<table width="100%"><!-- save buttons -->
<tr>
<td>
<?php if ($previd != "") { ?>
	<a href='?action=editrack&amp;id=<?php echo $previd?>'><button type="button"><img title='Previous Record' src='images/prev_rec.png' border=0><?php echo t("&nbsp; Previous Record")?></button></a>
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
<a href='?action=editrack&amp;id=<?php echo $nextid?>'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
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
