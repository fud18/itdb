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
<?php 

if (!isset($initok)) {echo "do not run this script directly";exit;}
/* Cory Funk 2015, cfunk@fhsu.edu */

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

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
$sql="SELECT * FROM jacks WHERE id = '' OR id != '' ";
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
  $perpage=25;
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
  echo "<h1>Jacks <a title='Add new jack' href='$scriptname?action=editjack&amp;id=new'>".
       "<img border=0 src='images/add.png'></a></h1>\n";
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
	 //"<th><a href='$fscriptname?$url&amp;orderby=jacks.id$ob'>ID</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=userdev$ob'>User / Device</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=switchname$ob'>Switch Name</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=locationid$ob'>Building [Floor]</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=locareaid$ob'>Area/Room</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=wallcoord$ob'>Wall</br>Location</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=jackname$ob'>Jack</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=deptname$ob'>Department</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=modport$ob'>Module.Port</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=vlan$ob'>VLAN</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=vlanname$ob'>VLAN Name</a></th>".
     "<th><a>Notes</a></th>".
	 "<th><button type='submit'><img border=0 src='images/search.png'></button></th>";

if ($export) {
 //clean links from excel export
  $thead = preg_replace('@<a[^>]*>([^<]+)</a>@si', '\\1 ', $thead); 
  $thead = preg_replace('@<img[^>]*>@si', ' ', $thead); 
}

echo $thead;
echo "</tr></thead>";
echo "<tbody>";
echo "<tr>";

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
$page=isset($_GET['page'])?$_GET['page']:1;

// Display Search Boxes
if (!$export) {

  echo "<td title='User or Device'><input style='width:25em' type=text value='$userdev' name='userdev'></td>";
  echo "<td title='Switch Name'><input type=text value='$switchname' name='switchname'></td>";?>
		<td><center><select id='locationid' name='locationid'>
			<option value=''><?php te("Select");?></option>
			<?php 
			foreach ($locations  as $key=>$location ) {
				$dbid=$location['id']; 
				$itype=$location['abbr'];
				$itype2=$location['floor'];
				$s="";
				if (($locationid=="$dbid")) $s=" SELECTED "; 
				echo "<option $s value='$dbid'>$itype [$itype2]</option>\n";
			}
			?>
			</select>
		</center></td>
<!-- end, Location Information -->

<!-- Room/Area Information -->
		<?php if (is_numeric($locationid))?>
		<td><center><select id='locareaid' name='locareaid'>
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
	echo "<td title='(N)orth<br />(S)outh<br />(E)ast<br />(W)est<br />'><center><select style='width:13em' name='wallcoord'>
			<option value=''>All</option>
			<option value='N'>N</option>
			<option value='S'>S</option>
			<option value='E'>E</option>
			<option value='W'>W</option>
		  </select></center></td>";
  echo "<td title='1A-100-1b'><input style='width:10em' type=text value='$jackname' name='jackname'></td>";?>
		<td title='Department Name'><center>
			<select style='width:auto' id='departmentsid' name='departmentsid'>
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
		<td title='tg.1.50<br />ge.1.1<br />fe.1.1<br />e.0.1'><center><input style='width:7em' type=text value='<?php $modport?>' name='modport' ></center></td>
<?php
	echo "<td title='VLAN'>";?>
			<select style='width:auto' id='vlanid' name='vlanid'>
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
			<select style='width:auto' id='vlanname' name='vlanname'>
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
	<td title='Notes'><input style='width:30em' type=text value='<?php $notes?>' name='notes'></td>
	<td></td>
<?php
}
echo "</tr>\n\n";


// Create WHERE clause
if (isset($userdev) && strlen($userdev)) $where.="AND userdev LIKE '%$userdev%' ";
if (isset($switchname) && strlen($switchname)) $where.="AND switchname LIKE '%$switchname%' ";
if (isset($locationid) && strlen($locationid)) $where.="AND locationid = '$locationid' ";
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
$sth=db_execute($dbh,"SELECT count(jacks.id) as totalrows FROM jacks WHERE id='' || id!='' $where");
$totalrows=$sth->fetchColumn();

//	Page Links
//	Get's the current page number
$get2=$_GET;
unset($get2['page']);
$url=http_build_query($get2);

//	Previous and Next Links
	$prev = $page - 1;
	$next = $page + 1;

//	Previous Page
	if ($page > 1){
	$prevlink .="<a href='$fscriptname?$url&amp;page=$prev'><img src='../images/previous-button.png' width='64' height='25' alt='previous' /></a> ";
	}else{
	$prevlink .="<img src='../images/previous-button.png' width='64' height='25' alt='previous' /> ";
	}

//	Numbers
	for ($plinks="",$pc=1;$pc<=ceil($totalrows/$perpage);$pc++){
		if ($pc==$page){
			$plinks.="<b><u><a href='$fscriptname?$url&amp;page=$pc'>[$pc]</a></u></b> ";
		}else{
			$plinks.="<a href='$fscriptname?$url&amp;page=$pc'>$pc</a> ";
		}
	}

//	Next Page
	if ($page < ceil($totalrows/$perpage)){
	$nextlink .="<a href='$fscriptname?$url&amp;page=$next'><img src='../images/next-button.png' width='64' height='25' alt='next' /></a> ";
	}else{
	$nextlink .=" <img src='../images/next-button.png' width='64' height='25' alt='next' />";
	}

//	Show All
	$alllink .="<a href='$fscriptname?$url&amp;page=all'><br /><img src='../images/view-all-button.gif' width='64' height='25' alt='show all' /></a> ";

///////////////////////////////////////////////////////////							end, Pagination							///////////////////////////////////////////////////////////

//page links
$get2=$_GET;
unset($get2['page']);
$url=http_build_query($get2);

for ($plinks="",$pc=1;$pc<=ceil($totalrows/$perpage);$pc++) {
 if ($pc==$page)
   $plinks.="<b><u><a href='$fscriptname?$url&amp;page=$pc'>$pc</a></u></b> ";
 else
   $plinks.="<a href='$fscriptname?$url&amp;page=$pc'>$pc</a> ";
}
$plinks.="<a href='$fscriptname?$url&amp;page=all'>[show all]</a> ";

$t=time();
$sql="SELECT * FROM jacks WHERE id = '' OR id != '' $where order by $orderby LIMIT $perpage OFFSET ".($perpage*($page-1));
$sth=db_execute($dbh,$sql);

// Display Results
$currow=0;
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
$currow++;

// Table Row
  if ($currow%2) $c="class='dark'";
  else $c="";

  echo "<tr $c>".
       ///////////////////////////////////////////////////////////////////////////"<td><a class='editiditm icon edit' title='Edit' href='$fscriptname?action=editjack&amp;id=".$r['id']."'><span>".$r['id']."</span></a>";

// Username
  $user=isset($userlist[$r['userid']])?$userlist[$r['userid']]['username']:"";

  
  	echo "</td>"?>
	<td><input style="width:25em" type=text value='<?php echo $r['userdev']?>' name='userdev'></td>
	<td><input type=text value='<?php echo $r['switchname']?>' name='switchname'></td>
<!-- Location Information -->
		<td><center><select style='width:auto' id='locationid' name='locationid'>
			<option value=''><?php te("Select");?></option>
			<?php 
			foreach ($locations  as $key=>$location ) {
				$dbid=$location['id']; 
				$itype=$location['abbr'];
				$itype2=$location['floor'];
				$s="";
				if (($locationid=="$dbid")) $s=" SELECTED "; 
				echo "<option $s value='$dbid'>$itype [$itype2]</option>\n";
			}
			?>
			</select>
		</center></td>
<!-- end, Location Information -->

<!-- Room/Area Information -->
		<?php if (is_numeric($locationid)) {
			$sql="SELECT * FROM locareas WHERE locationid=$locationid order by areaname";
			$sth=$dbh->query($sql);
			$locareas=$sth->fetchAll(PDO::FETCH_ASSOC);
		} 
		else 
			$locareas=array();
		?>
		<td><center><select style='width:auto' id='locareaid' name='locareaid'>
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
		</center></td>
		<?php 
			$N="";$S="";$E="";$W="";
				if ($r['wallcoord']=="N") {$N="checked";$S="";$E="";$W="";}
				if ($r['wallcoord']=="S") {$S="checked";$N="";$E="";$W="";}
				if ($r['wallcoord']=="E") {$E="checked";$N="";$S="";$W="";}
				if ($r['wallcoord']=="W") {$W="checked";$N="";$S="";$E="";}
		?>
	<td title='Select (N)orth, (S)outh, (E)ast, (W)est'>
		<input <?php echo $N?> class='radio' type=radio name='wallcoord' value='N'><?php te("N");?>
		<input <?php echo $S?> class='radio' type=radio name='wallcoord' value='S'><?php te("S");?>
		<input <?php echo $E?> class='radio' type=radio name='wallcoord' value='E'><?php te("E");?>
		<input <?php echo $W?> class='radio' type=radio name='wallcoord' value='W'><?php te("W");?>
	</td>
	<?php echo $r['wallcoord']?></center></td>
	<td><input style='width:10em' type=text value='<?php echo $r['jackname']?>' name='jackname'></td>
	<td><center><input style='width:7em' type=text value='<?php echo $departments[$r['departmentsid']]['abbr']?>' /></center></td>
	<td><center><input style='width:7em' type=text value='<?php echo $r['modport']?>' name='modport' /></center></td>
<!-- VLAN ID Information -->
	<td><select style='width:7em' id='vlanid' name='vlanid'>
		<option value='<?php echo $r['vlanid']?>'><?php echo $r['vlanid']?></option>
			<?php 
			foreach ($vlans as $key=>$v) {
				$dbid=$v['id']; 
				$itype=$v['vlanid'];
				$s="";
				if (($vlanid=="$dbid")) $s=" SELECTED "; 
				echo "<option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
		</td>
<!-- end, VLAN ID Information -->

<!-- VLAN Name Information -->
		<?php if (is_numeric($vlanid)) {
			$sql="SELECT * FROM vlans WHERE id=$vlanid order by vlanid";
			$sth=$dbh->query($sql);
			$vlans=$sth->fetchAll(PDO::FETCH_ASSOC);
		} 
		else 
			$vlans=array();
		?>
		<td><select style='width:7em' id='vlanname' name='vlanname'>
		<option value='<?php echo $r['vlanname']?>'><?php echo $r['vlanname']?></option>
			<?php 
			foreach ($vlans as $key=>$v ) {
				$dbid=$v['id']; 
				$itype=$v['vlanname'];
				$s="";
				if (($vlanid=="$dbid")) $s=" SELECTED "; 
				echo "<option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
		</td>
<!-- end, VLAN Name Information -->

<!--	<td><center><input style='width:7em' type=text value='<?php echo $vlans[$r['vlanid']]['vlanid']?>' /></center></td>
	<td><center><input style='width:7em' type=text value='<?php echo $vlans[$r['vlanname']]['vlanname']?>' /></center></td>
-->	<td><input style='width:30em' type=text value='<?php echo $r['notes']?>' /></td>
	<td><a href='?action=editjack&amp;id=<?php echo $r['id']?>'><img src="images/edit2.png" alt="Edit" /></a>
    	<input type='image' src="images/save.png" alt="Save" />
		<input type='image' src='images/delete.png' onclick='javascript:delconfirm2(\"{$r['id']}\",\"$scriptname?action=$action&amp;delid={$r['id']}\");'>
		<input type=hidden name='action' value='$action'>
		<input type=hidden name='id' value='$id'></td>
 <?php
}
$sth->closeCursor();

if ($export) {
  echo "</tbody>\n</table>\n";
  exit;
}
else {

?>
  </tbody>
  </table>
	<table width="100%"><!-- Bottom Search button -->
    <tr><td style='text-align:center' colspan=1><button type="submit"><img src='images/search.png'>Search</button></td></tr>
    </table>
  <input type='hidden' name='action' value='<?php echo $_GET['action']?>'>
  </form>

<?php  ///////////////////////////////////////////////////////////							Pagination Links							///////////////////////////////////////////////////////////?>

<div class='gray'>
  <br /><b><?php echo $totalrows?> results<br>
	<?php if ($page >= 1 && $page != "all"){
		echo $prevlink;
	}
	if ($page != "all"){
	// Function to generate pagination array - that is a list of links for pages navigation
    function paginate ($base_url, $query_str, $total_pages, $page, $perpage)
    {
        // Array to store page link list
        $page_array = array ();
        // Show dots flag - where to show dots?
        $dotshow = true;
        // walk through the list of pages
        for ( $i = 1; $i <= $total_pages; $i ++ )
        {
           // If first or last page or the page number falls 
           // within the pagination limit
           // generate the links for these pages
           if ($i == 1 || $i == $total_pages || 
                 ($i >= $page - $perpage && $i <= $page + $perpage) )
           {
              // reset the show dots flag
              $dotshow = true;
              // If it's the current page, leave out the link
              // otherwise set a URL field also
              if ($i != $page)
                  $page_array[$i]['url'] = $base_url . $query_str .
                                             "=" . $i;
              $page_array[$i]['text'] = strval ($i);
           }
           // If ellipses dots are to be displayed
           // (page navigation skipped)
           else if ($dotshow == true)
           {
               // set it to false, so that more than one 
               // set of ellipses is not displayed
               $dotshow = false;
               $page_array[$i]['text'] = "...";
           }
        }
        // return the navigation array
        return $page_array;
    }
    // To use the pagination function in a 
    // PHP script to display the list of links
    // paginate total number of pages ($pc) - current page is $page and show
    // 3 links around the current page
    $pages = paginate ("?$url&amp;", "page", ($pc - 1), $page, 3); ?>

    <?php 
    // list display
    foreach ($pages as $page) {
        // If page has a link
        if (isset ($page['url'])) { ?>
            <a href="<?php echo $page['url']?>">
    		<?php echo $page['text'] ?>
    	</a>
    <?php }
        // no link - just display the text
         else 
            echo $page['text'];
    }
	}?>
	<?php if ($page >= 1 && $page != "all"){
		echo $nextlink."<br />";
	}else
	?>
	<?php if ($page != "all"){
		echo $alllink."<br />";
	}else
	?>
	<a href='<?php echo "$fscriptname?action=$action&amp;export=1"?>'><img src='images/xcel2.jpg' height=25 border=0>Export to Excel
    
<?php  ///////////////////////////////////////////////////////////							end, Pagination	Links						///////////////////////////////////////////////////////////?>

<?php 
}

if ($export) {
  echo "\n</body>\n</html>\n";
  exit;
}

?>