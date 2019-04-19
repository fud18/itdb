<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
  $('table#EXjacklisttbl').dataTable({
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
/* Cory Funk 2019, cafunk@fhsu.edu */

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

$initok=1;
require("../../init.php");

// Get jack information
$sql="SELECT jacks.*, locations.nameid FROM jacks JOIN locations WHERE locations.id = locationid";
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
?>

<h1><?php te("Jacks");?> <a title='<?php te("Add new jack");?>' href='../../index.php?action=editjack&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table  class='display' width='100%' border=0 id='EXjacklisttbl'>

<thead>
<tr>
  <th style='width:70px'>Edit/Delete</th>
  <th>User / Device</th>
  <th>Switch Name</th>
  <th>Building [Floor]</th>
  <th>Area/Room</th>
  <th>Jack</th>
  <th>Department</th>
  <th>Module & Port</th>
  <th>VLAN</th>
  <th>VLAN Name</th>
  <th>Notes</th>
</tr>
</thead>

<tbody>
<?php 
//	How many records are in table
$sth=db_execute($dbh,"SELECT count(jacks.id) as totalrows, locations.nameid FROM jacks JOIN locations WHERE locations.id = locationid AND nameid = 00000");
$totalrows=$sth->fetchColumn();

$t=time();
$sql="SELECT jacks.*, locations.nameid FROM jacks JOIN locations WHERE locations.id = locationid AND nameid = 00000 $where ORDER BY switchname,mod,port LIMIT $totalrows";

$sth=db_execute($dbh,$sql);

$i=0;
/// print actions list
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  $i1++;
  $type="";

  echo "\n<tr id='trid{$r['id']}'>
  <td class='editiditm icon edit'><center><a href='../../index.php?action=editjack&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/deljack.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></td>
	<td style='width:25em'>".$r['userdev']."</td>
	<td>".$r['switchname']."</td>
	<td><center>".$locations[$r['locationid']]['abbr']."[".$locations[$r['locationid']]['floor']."]</center></td>
	<td><center>".$locareas[$r['locareaid']]['areaname']."</center></td>
	<td>".$r['jackname']."</td>
	<td><center>".$departments[$r['departmentsid']]['abbr']."</center></td>
	<td style='width:7em'><center>".$r['modport']."</center></td>
	<td><center>".$vlans[$r['vlanid']]['vlanid']."</center></td>
	<td><center>".$vlans[$r['vlanid']]['vlanname']."</center></td>
	<td style='width:20em'>".$r['notes']."</td></tr>";
}?>
</tbody>
</table>

</form>
</body>
</html>