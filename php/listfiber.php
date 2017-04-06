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
/* Cory Funk 2015, cfunk@fhsu.edu */

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

//delete Record
if (isset($_GET['delid'])) { //Deletes the record in the current row 
	$delid=$_GET['delid'];
	$sql="DELETE FROM fiber WHERE id=".$_GET['delid'];
	$sth=db_exec($dbh,$sql);
	echo "<script>document.location='$scriptname?action=listfiber'</script>";
	echo "<a href='$scriptname?action=listfiber'></a>"; 
	exit;
}


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

//expand: show more columns
if (isset($_GET['expand']) && $_GET['expand']==1) 
  $expand=1;
else 
  $expand=0;

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



/// display list
if ($export) 
  echo "\n<table border='1'>\n";
else {
  echo "<h1>Fiber Optics<a title='Add new fiber' href='$scriptname?action=editfiber&amp;id=new'>".
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
  $orderby="fiber.id asc";
elseif (isset($orderby)) {

  if (stristr($orderby,"FROM")||stristr($orderby,"WHERE")) {
    $orderby="fiber.id";
  }
  if (strstr($orderby,"DESC"))
    $ob="+ASC";
  else
    $ob="+DESC";
}

echo "<thead>\n";
$thead= "\n<tr><th><a href='$fscriptname?$url&amp;orderby=fiber.id$ob'>ID</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=fibertype$ob'>Fiber Type</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=intra_inter$ob'>Intra/Inter Fiber</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=light_guide$ob'>Light Guide</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=fiberstrnd$ob'>Fiber Strand</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=from_locationid$ob'>From Building</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=from_locareaid$ob'>From Area/Room</th>".
     "<th><a href='$fscriptname?$url&amp;orderby=from_jumper_no$ob'>From Jumper Number</a></th>".
     "<th><a href='$fscriptname?$url&amp;orderby=from_dev$ob'>From Device</th>".
     "<th><a href='$fscriptname?$url&amp;orderby=to_locationid$ob'>To Building</th>".
     "<th><a href='$fscriptname?$url&amp;orderby=to_locareaid$ob'>To Area/Room</th>".
     "<th><a href='$fscriptname?$url&amp;orderby=to_jumper_no$ob'>To Jumper Number</th>".
     "<th><a href='$fscriptname?$url&amp;orderby=to_dev$ob'>To Device</th>".
     "<th><a href='$fscriptname?$url&amp;orderby=notes$ob'>Notes</th>".
	 "<th><button type='submit'><img border=0 src='images/search.png'></button></th>";
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
$id=isset($_GET['id'])?($_GET['id']):"";
$fibertype=isset($_GET['fibertype'])?($_GET['fibertype']):"";
$intra_inter=isset($_GET['intra_inter'])?($_GET['intra_inter']):"";
$light_guide=isset($_GET['light_guide'])?($_GET['light_guide']):"";
$fiberstrnd=isset($_GET['fiberstrnd'])?($_GET['fiberstrnd']):"";
$from_locationid=isset($_GET['from_locationid'])?($_GET['from_locationid']):"";
$from_locareaid=isset($_GET['from_locareaid'])?$_GET['from_locareaid']:"";
$from_jumper_no=isset($_GET['from_jumper_no'])?$_GET['from_jumper_no']:"";
$from_dev=isset($_GET['from_dev'])?$_GET['from_dev']:"";
$to_locationid=isset($_GET['to_locationid'])?$_GET['to_locationid']:"";
$to_locareaid=isset($_GET['to_locareaid'])?$_GET['to_locareaid']:"";
$to_dev=isset($_GET['to_dev'])?$_GET['to_dev']:"";
$notes=isset($_GET['notes'])?$_GET['notes']:"";

///display search boxes
if (!$export) {
  echo "\n<td title='ID'></td>";
  echo "\n<td title='Fiber Type'><select name='fibertype'>
  		<option value=''></option>
  		<option value='MultiMode'>MultiMode</option>
  		<option value='SingleMode'>SingleMode</option>
		</select>";
  echo "\n<td title='IntraBuilding or InterBuilding Connectivity'><select name='intra_inter'>
  		<option value=''></option>
  		<option value='intra'>IntraBuilding</option>
  		<option value='inter'>InterBuilding</option>";
		?>
		<td><select id='light_guide' name='light_guide'>
			<option value=''><?php te("");?></option>
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
		</td>";
<?php
		echo "<td title='Fiber Strand Number'>";?><select id='fiberstrnd' name='fiberstrnd'>
				<option value=''><?php te("");?></option>
				<?php
					for( $i= 1 ; $i < 289 ; $i++ )
					{
						echo '<option ' . ($i == 0 ? 'selected=\'selected\'' : '') . ' value="' . $i . '" >' . $i . '</option>';
					}
                ?></select></td>

<!-- Location Information -->
		<td><select id='from_locationid' name='from_locationid'>
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
<!-- end, Location Information -->


<!-- Room/Area Information -->
		<?php if (is_numeric($from_locationid))?>
		<td><center><select id='from_locareaid' name='from_locareaid'>
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
<!-- end, Room/Area Information -->

<?php
		echo "<td title='Jumper Number (From)'>";?><center><select id='from_jumper_no' name='from_jumper_no'>
				<option value=''><?php te("");?></option>
				<?php
					for( $i= 1000 ; $i < 2501 ; $i++ )
					{
						echo '<option ' . ($i == 0 ? 'selected=\'selected\'' : '') . ' value="' . $i . '" >' . $i . '</option>';
					}
                ?></select></center></td>
		<td title='From Device'><input type=text name='from_dev'></td>
        
<!-- Location Information -->
		<td><select id='to_locationid' name='to_locationid'>
			<option value=''></option>
			<?php 
			foreach ($locations  as $key=>$location ) {
				$dbid=$location['id']; 
				$itype=$location['name'];
				$s="";
				if (($to_locationid=="$dbid")) $s=" SELECTED "; 
				echo "    <option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select>
		</td>
<!-- end, Location Information -->


<!-- Room/Area Information -->
		<?php if (is_numeric($to_locationid))?>
		<td><center><select id='to_locationid' name='to_locationid'>
			<option value=''></option>
			<?php 
			foreach ($locareas  as $key=>$locarea ) {
				$dbid=$locarea['id']; 
				$itype=$locarea['areaname'];
				$s="";
				if (($to_locationid=="$dbid")) $s=" SELECTED "; 
				echo "    <option $s value='$dbid'>$itype</option>\n";
			}
			?>
			</select></center>
		</td>
<!-- end, Room/Area Information -->
<?php
		echo "<td title='Jumper Number (To)'>";?><center><select ' id='to_jumper_no' name='to_jumper_no'>
				<option value=''><?php te("");?></option>
				<?php
					for( $i= 1000 ; $i < 2501 ; $i++ )
					{
						echo '<option ' . ($i == 0 ? 'selected=\'selected\'' : '') . ' value="' . $i . '" >' . $i . '</option>';
					}
                ?></select></center></td>
		<td title='To Device'><input type=text name='to_dev'></td>
		<td title='Notes'><input type=text name='notes'></td>
<?php
}//if not export to excel: searchboxes

/// create WHERE clause
$where="";
if (strlen($id)) $where.="AND id = '$id' ";
if (isset($fiberid) && strlen($fiberid)) $where.="AND fiberid='$fiberid' ";
if (isset($fibername) && strlen($fibername)) $where.="AND fibername LIKE '%$fibername%' ";

///////////////////////////////////////////////////////////							Pagination							///////////////////////////////////////////////////////////

//	How many records are in table
$sth=db_execute($dbh,"SELECT count(fiber.id) as totalrows FROM fiber WHERE id = '' OR id != '' $where");
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
$sql="SELECT fiber.* FROM fiber WHERE id = '' OR id != '' $where order by $orderby LIMIT $perpage OFFSET ".($perpage*($page-1));
$sth=db_execute($dbh,$sql);

/// display results
$currow=0;
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
$currow++;

  //table row
  if ($currow%2) $c="class='dark'";
  else $c="";

  echo "\n<tr $c>".
       "<td><a class='editiditm icon edit' title='Edit' href='$fscriptname?action=editfiber&amp;id=".$r['id']."'><span>Edit</span></a>";

  //username
  $user=isset($userlist[$r['userid']])?$userlist[$r['userid']]['username']:"";


  echo	"</td>".
		"\n  <td>".$r['fibertype']."</td>".
		"\n  <td>".$r['intra_inter']."</td>".
		"\n  <td>".$r['light_guide']."</td>".
		"\n  <td>".$r['fiberstrnd']."</td>".
		"\n  <td>".$locations[$r['from_locationid']]['name']."</td>\n".
		"\n  <td><center>".$locareas[$r['from_locareaid']]['areaname']."</center></td>\n".
		"\n  <td><center>".$r['from_jumper_no']."</center></td>".
		"\n  <td>".$r['from_dev']."</td>".
		"\n  <td>".$locations[$r['to_locationid']]['name']."</td>\n".
		"\n  <td><center>".$locareas[$r['to_locareaid']]['areaname']."</center></td>\n".
		"\n  <td><center>".$r['to_jumper_no']."</center></td>".
		"\n  <td>".$r['to_dev']."</td>".
		"\n  <td>".$r['notes']."</td>";
	echo "<td><center><input type='image' src='images/delete.png' onclick='javascript:delconfirm2(\"{$r['id']}\",\"$scriptname?action=$action&amp;delid={$r['id']}\");'>".
    "<input type=hidden name='action' value='$action'>".
    "<input type=hidden name='id' value='$id'>";
	}
  echo "</td></tr>";
$sth->closeCursor();
if ($export) {
  echo "</tbody>\n</table>\n";
  exit;
}
else {
  if ($expand) 
    $cs=25;
  else 
    $cs=15;
?>
  <tr><td colspan='<?php echo $cs?>' class=tdc><button type=submit><img src='images/search.png'>Search
  </tbody></table>
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