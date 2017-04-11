<?php
/* Spiros Ioannou 2009-2010 , sivann _at_ gmail.com */

//error_reporting(E_ALL);				//***UNCOMMENT THESE 2 LINES TO SEE ERRORS ON THE PAGE***
//ini_set('display_errors', '1');

//delete user
if (isset($_GET['delid'])) {
  $delid=$_GET['delid'];
  if (!is_numeric($delid)) {
    echo "Non numeric id delid=($delid)";
    exit;
  }

  //first handle item associations
  /*
  $nitems=countitemsofuser($delid);
  if ($nitems>0) {
    echo "<b>User not deleted: Please reassign $nitems items first from this user<br></b>\n";
    echo "<br><a href='javascript:history.go(-1);'>Go back</a>\n</body></html>";
    exit;
  }
  else {
  }
    */

  deluser($delid,$dbh); //reassigns items to administrator
  echo "<script>document.location='$scriptname?action=listusers'</script>\n";
  echo "<a href='$scriptname?action=listusers'>Go here</a>\n</body></html>"; 
  exit;

}

if (isset($_POST['id'])) { //if we came from a post (save), update the user 
  $id=$_POST['id'];
  $username=$_POST['username'];
  $usertype=$_POST['usertype'];

  //don't accept empty fields
  if (empty($_POST['username']))  {
    echo "<br><b><span class='mandatory'>Username</span> field cannot be empty.</b><br>".
         "<a href='javascript:history.go(-1);'>Go back</a></body></html>";
    exit;
  }
  if ($_POST['id']=="new")  {//if we came from a post (save) the add user
    $sql="INSERT into users (username , userdesc , pass, usertype) ".
	 " VALUES ('$username','$userdesc','$hashedPass', '$usertype')";
    db_exec($dbh,$sql,0,0,$lastid);
    $lastid=$dbh->lastInsertId();
    print "<br><b>Added user <a href='$scriptname?action=$action&amp;id=$lastid'>$lastid</a></b><br>";
    echo "<script>window.location='$scriptname?action=$action&id=$lastid'</script> "; //go to the new user
    echo "\n</body></html>";
    $id=$lastid;
    exit;

  }//new rack
  else {
    //check for duplicate username
    $sql="SELECT count(id) AS count from users where username='{$_POST['username']}' AND id<>{$_POST['id']}";
    $sth1=db_execute($dbh,$sql);
    $r1=$sth1->fetch(PDO::FETCH_ASSOC);
    $sth1->closeCursor();
    $c=$r1['count'];
    if ($c) {
      echo "<b>Not saved -- Username already exists</b>";
    }
    else {
		$hashedPass = password_hash($pass, PASSWORD_BCRYPT);
        if ($username=='admin' && $usertype) {
            echo "<h2>".t("User Admin has always full access")."</h2><br>";
            $usertype=0;
        }
			if ($chngpass==''){
          $sql="UPDATE users set ".
        " username='".$_POST['username']."', ".
        " userdesc='".$_POST['userdesc']."', ".
        " usertype='".$usertype."' ".
        " WHERE id=$id";
          db_exec($dbh,$sql);
		}else{
			  $sql="UPDATE users set ".
			" username='".$_POST['username']."', ".
			" userdesc='".$_POST['userdesc']."', ".
			" pass='".$hashedPass."', ".
			" usertype='".$usertype."' ".
			" WHERE id=$id";
			  db_exec($dbh,$sql);
		}
    }
  }
}//save pressed

if ($id!="new") {
  //get current item data
  $id=$_GET['id'];
  $sql="SELECT * FROM users WHERE id='$id'";
  $sth=db_execute($dbh,$sql);
  $user=$sth->fetchAll(PDO::FETCH_ASSOC);
  
	//  Next & Previous Buttons' Function
	$curid = intval($user[0]);

    // Select contents from the selected id
    $sql = "SELECT * FROM users WHERE id='$curid'";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $info = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die('Not found');
    }

    // Next Record
    $sql = "SELECT id FROM users WHERE id>'$id' LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $nextresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$nextid = strval($nextresults[0]['id']);
    }

    // Previous Record
    $sql = "SELECT id FROM users WHERE id<'$id' ORDER BY id DESC LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $prevresults = $result->fetchAll(PDO::FETCH_ASSOC);
		$previd = strval($prevresults[0]['id']);
    }
} else {
    // No form has been submitted so use the lowest id and grab its info
    $sql = "SELECT * FROM users WHERE id > 0 LIMIT 1";
    $result = db_execute($dbh,$sql);
    if ($result>0) {
        $inforesults = $result->fetchAll(PDO::FETCH_ASSOC);
		$info =  strval($inforesults[0]['id']);
		
    }
}

///////////////////////////////// display data 

if (!isset($_REQUEST['id'])) {echo "ERROR:ID not defined";exit;}
$id=$_REQUEST['id'];

$sql="SELECT * from users where users.id='$id'";
$sth=db_execute($dbh,$sql);
$r=$sth->fetch(PDO::FETCH_ASSOC);

if (($id !="new") && (count($r)<2)) {echo "ERROR: non-existent ID<br>($sql)";exit;}

echo "\n<form id='mainform' method=post  action='$scriptname?action=$action&amp;id=$id' enctype='multipart/form-data'  name='addfrm'>\n";

if ($id=="new")
  echo "\n<h1>".t("Add User")."</h1>\n";
else
  echo "\n<h1>".t("Edit User")."  ($id)"."</h1>\n";

?>

<!-- error errcontainer -->
<div class='errcontainer ui-state-error ui-corner-all' style='padding: 0 .7em;width:700px;margin-bottom:3px;'>
        <p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span>
        <h4><?php te("There are errors in your form submission, please see below for details");?>.</h4>
        <ol>
                <li><label for="username" class="error"><?php te("Username is missing");?></label></li>
        </ol>
</div>

<table style='width:100%' border=0>


<tr>
<td class="tdtop" width=20%>

    <table class="tbl2" style='width:300px;'>
    <tr><td colspan=2><h3>User Properties</h3></td></tr>
    <tr><td class="tdt">ID:</td> 
        <td><input  style='display:none' type=text name='id' 
	     value='<?php echo $id?>' readonly size=3><?php echo $id?></td></tr>
    <tr><td class="tdt"><?php te("Username");?>:</td> <td><input  class='input2 mandatory' validate='required:true' size=20 type=text name='username' value="<?php echo $r['username']?>"></td></tr>
    <tr><td class="tdt"><?php te("Type")?></td>
        <td>
	<select class='mandatory' validate='required:true' name='usertype'>
	<?php
	if ($r['usertype']==1 || empty($r['username'])) {$s1="selected"; $s0="";} else {$s0="selected"; $s1="";} 
	echo " <option value=1 $s1>".t("Read Only")."</option>\n".
	     " <option value=0 $s0>".t("Full Access")."</option>\n".
	     "</select></td>";
	?>
	</select>
    </td></tr>

    <tr><td class="tdt"><?php te("User Description");?>:</td> 
        <td><input autocomplete="off" class='input2' size=20 
	     type=text name='userdesc' value="<?php echo $r['userdesc']?>">
        </td></tr>
    <tr><td class="tdt"><?php te("Password");?>:</td> 
        <td><input autocomplete="off" class='input2' size=20 type="password"
	     name='pass' value="">
	 </td></tr>
	<?php
    function IsChecked($chkname,$value)
    {
        if(!empty($_POST[$chkname]))
        {
            foreach($_POST[$chkname] as $chkval)
            {
                if($chkval == $value)
                {
                    return true;
                }
            }
        }
        return false;
    }
?>
    <tr><td class="tdt"><?php te("Change Password");?>:</td> 
        <td><input type="checkbox" name="chngpass" value="Yes" />
	 </td></tr>
    <tr><td class="tdt"><?php te("Items");?>:</td> <td><?php echo countitemsofuser($r['id']) ?></td>
    </table>
    <ul>
      <li><b><?php te("Users are used for both web login and as item assignees");?></b></li>
      <li><sup>1</sup><?php te("Blank passwords prohibit login");?></li>
      <li><?php te("Add user image from the files page Type = Avatar");?></li>
    </ul>
    <br />
    <br />
    
    <?php
	$sql="SELECT *
	FROM users, files
	WHERE fname LIKE '%{$r['username']}%'
	AND username='".$r['username']."'
	AND type='12'";
  $sth=db_execute($dbh,$sql);
  $u=$sth->fetchAll(PDO::FETCH_ASSOC);
	?>
    
		<center>
				    <?php 
					$pictureName=$u[0]['fname'];
					if ($pictureName != ""){
						echo "<a href='../data/files/avatar/".$pictureName."'><img style='max-width: 400px; max-height: 200px' src='data/files/avatar/".$pictureName."'><br />";$pictureName;	
					}else{
						echo "<a href='../data/files/avatar/na-avatar.png'><img style='max-width: 400px; max-height: 200px' src='data/files/avatar/na-avatar.png'>";
					}
					?>
		</center>
	<br />
    <br />

</td>

<td class='smallrack' style='padding-left:10px;border-left:1px dashed #aaa'>
    <div class=scrltblcontainer>
      <div  id='items' class='relatedlist'><?php te("ITEMS");?></div>
      <?php 
      if (is_numeric($id)) {
        $sql="SELECT items.id, agents.title || ' ' || items.model || ' [' || itemtypes.typedesc || ', ".
             " ID:' || items.id || ']' as txt ".
             "FROM agents,items,itemtypes WHERE ".
             " agents.id=items.manufacturerid AND items.itemtypeid=itemtypes.id AND ".
             " items.userid='$id' ";
        $sthi=db_execute($dbh,$sql);
        $ri=$sthi->fetchAll(PDO::FETCH_ASSOC);
        $nitems=count($ri);
        $institems="";
        for ($i=0;$i<$nitems;$i++) {
          $x=($i+1).": ".$ri[$i]['txt'];
          if ($i%2) $bcolor="#D9E3F6"; else $bcolor="#ffffff";
          $institems.="\t<div style='margin:0;padding:0;background-color:$bcolor'>".
                      "<a href='$scriptname?action=edititem&amp;id={$ri[$i]['id']}'>$x</a></div>\n";
        }
        echo $institems;
      }
      ?>
      </div>
    </div>
</td>
</tr>
</table>

<table width="100%"><!-- save buttons -->
<tr>
<td>
<?php if ($previd != "") { ?>
	<a href='?action=edituser&amp;id=<?php echo $previd?>'><button type="button"><img title='Previous Record' src='images/prev_rec.png' border=0><?php echo t("&nbsp; Previous Record")?></button></a>
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
<a href='?action=edituser&amp;id=<?php echo $nextid?>'><button type="button"><?php echo t("Next Record &nbsp;")?><img title='Next Record' src='images/next_rec.png' border=0></button></a>
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