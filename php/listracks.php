<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
  $('table#rackslisttbl').dataTable({
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"iDisplayLength": 25,
	//"aLengthMenu": [[10,25, 50, 100, -1], [10,25, 50, 100, "All"]],
	"bLengthChange": true,
	"bFilter": true,
	"bSort": true,
	"bInfo": true,
	"sDom": '<"H"Tlpf>rt<"F"ip>',
	"oTableTools": {
	    "sSwfPath": "swf/copy_cvs_xls_pdf.swf"
	}

  });
});

</SCRIPT>
<?php 

if (!isset($initok)) {echo "do not run this script directly";exit;}

$sql="SELECT id,name FROM locations order by name";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $locations[$r['id']]=$r;

$sql="SELECT id,areaname FROM locareas order by areaname";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $locareas[$r['id']]=$r;


$sql="SELECT count(items.id) AS population, sum(items.usize) as occupation,racks.* FROM racks LEFT OUTER JOIN items ON items.rackid=racks.id GROUP BY racks.id ORDER BY locationid,locareaid";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("Racks");?> <a title='<?php te("Add new Rack");?>' href='<?php echo $scriptname?>?action=editrack&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>


<table class='display' width="100%" id='rackslisttbl'>

<thead>
<tr>
  <th width='2%'><?php te("Edit/Delete");?></th>
  <th width='5%'><?php te("Occupation");?></th>
  <th title='<?php te("how many items are assigned to this rack");?>'> <?php te("Items");?></th>
  <th width='10%'><?php te("Size (U)");?><sup>*</sup></th>
  <th><?php te("Depth");?></th>
  <th><?php te("Location");?></th>
  <th><?php te("Area/Room");?></th>
  <th><?php te("Label");?></th>
</tr>
</thead>
<tbody>

<?php 

$i=0;
/// print actions list
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  $i++;

  $occupation=(int)$r['occupation'];
  echo "\n<tr id='trid{$r['id']}'>";
  echo "<td class='editiditm icon edit'><center><a href='$scriptname?action=editrack&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/delrack.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></td>";
  echo "<td title='$occupation U occupied' >".
       "<div style='width:70px;border:1px solid #888;padding:0;'>\n".
       "<div style='background-color:#8ECE03;width:".(int)($occupation/$r['usize']*100/(100/70))."px'>&nbsp;</div></div></td>\n";
  echo "<td>{$r['population']}</td>\n";
  echo "<td>{$r['usize']}U</td>\n";
  if(strlen($r['depth'])) $depth=$r['depth']."mm";
  echo "<td>$depth</td>\n";
  echo "<td>".$locations[$r['locationid']]['name']."</td>\n";
  echo "<td>".$locareas[$r['locareaid']]['areaname']."</td>\n";
  echo "<td>{$r['label']}</td></tr>\n";

 
}
?>

</tbody>
</table>

</form>
</body>
</html>
