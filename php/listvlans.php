<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
 //$('input#vlanlistfilter').quicksearch('table#vlanlisttbl tbody tr');

  $('table#vlanlisttbl').dataTable({
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "iDisplayLength": -1,
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

/* Cory Funk 2018, cafunk _at_ scatcat.fhsu.edu */

$sql="SELECT * from vlans ";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("VLANS");?> <a title='<?php te("Add new vlan");?>' href='<?php echo $scriptname?>?action=editvlan&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table  class='display' width='100%' border=0 id='vlanlisttbl'>

<thead>
<tr>
  <th style='width:70px'><?php te("Edit/Delete");?></th>
  <th style='width:10px'><?php te("VLAN");?></th>
  <th style='width:200px'><?php te("VLAN Name");?></th>
  <th style='width:200px'><?php te("VLAN IP");?></th>
  <th style='width:100px'><?php te("VLAN CIDR");?></th>
  <th style='width:200px'><?php te("VLAN Subnet");?></th>
  <th><?php te("VLAN Notes");?></th>
</tr>
</thead>
<tbody>
<?php

$i=0;
/// print actions list
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  echo "\n<tr id='trid{$r['id']}'>";
  echo "<td><div class='editiditm icon edit'><center><a href='$scriptname?action=editvlan&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/delvlan.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></div></td>";
  echo "<td style='padding-left:2px;padding-right:2px;'><center>{$r['vlanid']}</center></td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['vlanname']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'><center>{$r['vlanip']}</center></td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'><center>{$r['vlancidr']}</center></td>\n";
  echo "<td style='padding-left:10px;padding-right:2px;'>{$r['vlansubnet']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['vlannotes']}</td>\n";
}?>

</tbody>
</table>