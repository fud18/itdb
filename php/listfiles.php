<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
 //$('input#filelistfilter').quicksearch('table#filelisttbl tbody tr');

  $('table#fileslisttbl').dataTable({
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "iDisplayLength": 50,
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

$sql="SELECT * from files";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("Files");?> <a title='<?php te("Add new file");?>' href='<?php echo $scriptname?>?action=editfiles&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table class='display' border=0 id='fileslisttbl'>

<thead>
<tr>
  <th style='width:70px'><?php te("Edit/Delete");?></th>
  <th><?php te("Type");?></th>
  <th><?php te("Title");?></th>
  <th><?php te("File");?></th>
  <th><?php te("Associations");?></th>
</tr>
</thead>

<tbody>
<?php 
$i=0;
/// print actions list
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
  $i++;
  $nlinks=countfileidlinks($r['id'],$dbh);
  $type=$r['typedesc'];
//CSS crap
  if ($type=="invoice") $type="<span style='color:#0076A0'>$type</span>";
  if (!($i%2)) $cl="class=dark";else $cl="";


  echo "\n<tr $cl id='trid{$r['id']}'>";
  echo "<td class='editiditm icon edit'><center><a href='$scriptname?action=editfile&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/delfile.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></td>";
  echo "<td style='padding-left:2px;padding-right:2px;'>".(($r['type'] == "1") ? "photo" : (($r['type'] == "2") ? "manual" : (($r['type'] == "3") ? "invoice" : (($r['type'] == "4") ? "offer" : (($r['type'] == "5") ? "order" : (($r['type'] == "6") ? "service" : (($r['type'] == "7") ? "report" : (($r['type'] == "8") ? "license" : (($r['type'] == "9") ? "other" : (($r['type'] == "10") ? "contract" : (($r['type'] == "11") ? "floorplan" : (($r['type'] == "12") ? "avatar" : ""))))))))))))."</td>\n";
  echo "<td style='padding-left:2px;padding-right:2px;'>{$r['title']}</td>\n";
  echo "<td><a class='smaller' target=_blank href='$uploaddirwww{$r['fname']}'>{$r['fname']}</a></td>\n";
  if (!$nlinks)
    echo "<td style='background-color:#EAAF0F'>$nlinks</td>\n";
  else
    echo "<td>$nlinks</td>\n";
  echo "</tr>\n";}?>

</tbody>
</table>