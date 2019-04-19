<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
 //$('input#itemlistfilter').quicksearch('table#itemlisttbl tbody tr');

  $('table#itemlisttbl').dataTable({
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "iDisplayLength": 25,
                "aLengthMenu": [[10,25, 50, 100, -1], [10,25, 50, 100, "All"]],
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
/* Spiros Ioannou 2009 , sivann _at_ gmail.com */

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

//	Get item types
$sql="SELECT * from itemtypes order by typedesc";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $itypes[$r['id']]=$r;
$sth->closeCursor();
$sql="SELECT * from items order by id";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $itemlist[$r['id']]=$r;
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
$sql="SELECT * from racks";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $racks[$r['id']]=$r;
$sth->closeCursor();
$sql="SELECT * from tags order by name";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $tags[$r['id']]=$r;
$sth->closeCursor();
$sql="SELECT * from vlans order by id";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $vlans[$r['id']]=$r;
$sth->closeCursor();
$sql="SELECT * from departments order by id";
$sth=$dbh->query($sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $departments[$r['id']]=$r;
$sth->closeCursor();
$sql="SELECT id,title FROM agents";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $agents[$r['id']]=$r;
$sth->closeCursor();
$sql="SELECT * FROM statustypes";
$sth=$dbh->query($sql);
$statustypes=$sth->fetchAll(PDO::FETCH_ASSOC);
?>

<h1><?php te("Items");?> <a title='<?php te("Add new item");?>' href='<?php echo $scriptname?>?action=edititem&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table  class='display' width='100%' border=0 id='itemlisttbl'>

<thead>
<tr>
  <th style='width:70px'><?php te("Edit/Delete");?></th>
  <th><?php te("Item type");?></th>
  <th><?php te("Building [Floor]");?></th>
  <th><?php te("Area/Room");?></th>
  <th><?php te("Manufacturer");?></th>
  <th style='width:70px'><?php te("Model");?></th>
  <th><?php te("DNS Name");?></th>
  <th><?php te("S/N");?></th>
  <th><?php te("Asset #");?></th>
  <th><?php te("Status");?></th>
  <th><?php te("MAC");?></th>
  <th><?php te("IPv4");?></th>
  </tr>
</thead>
<tbody>
<?php 
$t=time();
$sql="SELECT items.*,agents.title AS agtitle, (purchasedate+warrantymonths*30*24*60*60-$t)/(60*60*24) AS remdays FROM items, agents WHERE agents.id=items.manufacturerid $where ORDER BY function ASC";
$sth=db_execute($dbh,$sql);

  $x=attrofstatus((int)$r['status'],$dbh);
  $attr=$x[0];
  $statustxt=$x[1];

// display results
$currow=0;
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
$currow++;

  $sn="";
  if (strlen($r['sn'])) $sn.=$r['sn'];
  if (strlen($r['sn2'])) {if (strlen($sn)) $sn.=", ";} $sn.=$r['sn2'];
  if (strlen($r['sn3'])) {if (strlen($sn)) $sn.=", ";} $sn.=$r['sn3'];

  //username
  $user=isset($userlist[$r['userid']])?$userlist[$r['userid']]['username']:"";
  if ($r['ports']) $ports=$r['ports'];

  echo "\n<tr id='trid{$r['id']}'>";
  echo "<td class='editiditm icon edit'><center><a href='$scriptname?action=edititem&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/delitem.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></td>";
  echo "<td>".$itypes[$r['itemtypeid']]['typedesc']."</td>";
  echo "<td>".$locations[$r['locationid']]['name']."</td>\n";
  echo "<td><center>".$locareas[$r['locareaid']]['areaname']."</center></td>";
  echo "<td>".$r['agtitle']."&nbsp;</td>";
  echo "<td width='auto'><center>".$r['model']."</center></td>";
  echo "<td><center>".$r['dnsname']."</center></td>";
  echo "<td><center>$sn</center></td>";
  echo "<td><center>".$r['asset']."</center></td>";
  echo "<td><center>$statustxt</center></td>";
  echo "<td>".$r['macs']."</td>";
  echo "<td>".$r['ipv4']."</td>";
}?>

</tbody>
</table>

</form>
</body>
</html>
