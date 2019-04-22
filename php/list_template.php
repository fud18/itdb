<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
 //$('input#fiberlistfilter').quicksearch('table#fiberlisttbl tbody tr');

  $('table#fiberlisttbl').dataTable({
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

/* Spiros Ioannou 2009 , sivann _at_ gmail.com */

$sql="SELECT * from fiber order by fibertype,fiberstrnd";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("Fiber Optics");?> <a title='<?php te("Add new fiber");?>' href='<?php echo $scriptname?>?action=editfiber&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table class='display' border=0 id='fiberlisttbl'>

<thead>
<tr>
  <th style='width:70px'><?php te("Edit/Delete");?></th>
  <th><?php te("Fiber Type");?></th>
  <th><?php te("Intra/Inter<br/>Building");?></th>
  <th><?php te("Light<br/>Guide");?></th>
  <th><?php te("Strand #");?></th>
  <th><?php te("From<br/>Building");?></th>
  <th><?php te("From<br/>Room");?></th>
  <th><?php te("From<br/>Jumper #");?></th>
  <th><?php te("From<br/>Device");?></th>
  <th><?php te("To<br/>Building");?></th>
  <th><?php te("To<br/>Room");?></th>
  <th><?php te("To<br/>Jumper #");?></th>
  <th><?php te("To<br/>Device");?></th>
  <th><?php te("Notes");?></th>
</tr>
</thead>

<tbody>
<?php 
$i=0;
/// print actions list
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  $i++;
  echo "\n<tr id='trid{$r['id']}'>";
  echo "<td class='editiditm icon edit'><center><a href='$scriptname?action=editagent&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/delagent.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></td>";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['fibertype']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['intra_inter']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['light_guide']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['fiberstrnd']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['from_locationid']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['from_locareaid']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['from_jumper_no']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['from_dev']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['to_locationid']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['to_locareaid']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['to_jumper_no']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['to_dev']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['notes']}</td>\n";
}?>

</tbody>
</table>