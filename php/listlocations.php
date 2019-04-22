<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
 //$('input#locationlistfilter').quicksearch('table#locationlisttbl tbody tr');

  $('table#locationlisttbl').dataTable({
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

/* Spiros Ioannou 2010 , sivann _at_ gmail.com */

$sql="SELECT locations.*,group_concat(locareas.areaname,', ') AS areaname FROM locations ".
     " LEFT OUTER JOIN locareas ON locareas.locationid=locations.id GROUP BY locations.id ORDER BY name, floor";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("Locations");?> <a title='<?php te("Add new Location");?>' href='<?php echo $scriptname?>?action=editlocation&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table  class='display' width='100%' border=0 id='locationlisttbl'>

<thead>

<tr>
  <th width='2%'><?php te("Edit/Delete");?></th>
  <th width='20%' nowrap><?php te("Location/Building Name");?></th>
  <th width='10%'><?php te("Floor");?></th>
  <th width='40%'><?php te("Areas/Rooms");?></th>
  <th><?php te("Floor Plan");?></th>
</tr>
</thead>
<tbody>
<?php 

$i=0;
/// print actions list
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  $i1++;
  $type="";

  echo "\n<tr id='trid{$r['id']}'>";
  echo "<td><div class='editiditm icon edit'><center><a href='$scriptname?action=editlocation&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/dellocation.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></div></td>";
  echo "<td>{$r['name']}</td>\n";
  echo "<td>{$r['floor']}</td>\n";
  echo "<td>{$r['areaname']}</td>\n";
  echo "<td><center>{$r['floorplanfn']}</center></td>\n";
  echo "</tr>\n";
}
?>

</tbody>
</table>

</form>
</body>
</html>