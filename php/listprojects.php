<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
  $('table#projectlisttbl').dataTable({
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

$sql="SELECT * from projects";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("Projects");?> <a title='<?php te("Add new projects");?>' href='<?php echo $scriptname?>?action=editproject&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table  class='display' width='100%' border=0 id='projectlisttbl'>

<thead>
<tr>
  <th style='width:70px'><?php te("Edit/Delete");?></th>
  <th><?php te("Project Name");?></th>
  <th><?php te("Building");?></th>
  <th><?php te("Area / Room");?></th>
  <th><?php te("Brief Summary");?></th>
  <th><?php te("Project Status");?></th>
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
  echo "<td><div class='editiditm icon edit'><center><a href='$scriptname?action=editproject&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/delproject.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></div></td>";
  echo "<td>".$r['projectname']."</td>";
  echo "<td>".$locations[$r['locationid']]['name']."</td>";
  echo "<td><center>".$locareas[$r['locareaid']]['areaname']."</center></td>";
  echo "<td>".$r['summary']."</td>";
  echo "<td>".$r['proj_status']."</td></tr>";
  }
?>

</tbody>
</table>

</form>
</body>
</html>
