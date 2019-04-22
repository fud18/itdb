<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
 //$('input#departmentlistfilter').quicksearch('table#departmentlisttbl tbody tr');

  $('table#departmentlisttbl').dataTable({
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

$sql="SELECT * from departments order by division,name";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("Departments");?> <a title='<?php te("Add new department");?>' href='<?php echo $scriptname?>?action=editdepartment&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table class='display' border=0 id='departmentlisttbl'>

<thead>
<tr>
  <th style='width:70px'><?php te("Edit/Delete");?></th>
  <th style='width:600px'><?php te("Division");?></th>
  <th style='width:600px'><?php te("Department Name");?></th>
  <th style='width:400px'><?php te("Department<br />Abbr.");?></th>
</tr>
</thead>

<tbody>
<?php 
$i=0;
/// print actions list
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  $i++;
  echo "\n<tr id='trid{$r['id']}'>";
  echo "<td class='editiditm icon edit'><center><a href='$scriptname?action=editdepartment&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/deldepartment.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></td>";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['division']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['name']}</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['abbr']}</td>\n";
}?>

</tbody>
</table>