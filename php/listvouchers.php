<SCRIPT LANGUAGE="JavaScript"> 
$(function () {
 //$('input#voucherlistfilter').quicksearch('table#voucherlisttbl tbody tr');

  $('table#voucherlisttbl').dataTable({
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
//	How many records are in table

$sql="SELECT * from vouchers";
$sth=db_execute($dbh,$sql);
?>

<h1><?php te("Vouchers");?> <a title='<?php te("Add new voucher");?>' href='<?php echo $scriptname?>?action=editvoucher&amp;id=new'><img border=0 src='images/add.png' ></a>
</h1>

<table  class='display' width='100%' border=0 id='voucherlisttbl'>

<thead>
<tr>
  <th style='width:70px'><?php te("Edit/Delete");?></th>
  <th><?php te("Voucher");?></th>
  <th><?php te("Issue Date");?></th>
  <th><a title='<?php te("1 Day</br>3 Days</br>1 Week</br>1 Month</br>3 Months</br>9 Months</br>1 Year");?>'</a><?php te("Valid Time Length (Days)");?></th>
  <th><?php te("User");?></th>
  <th><?php te("Assigned By");?></th>
  <th><?php te("Notes");?></th>
</tr>
</thead>
<tbody>
<?php 

// Display Results
$currow=0;
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
$currow++;

// Table Row
  if ($currow%2) $c="class='dark'";
  else $c="";

  echo "\n<tr $c>".
       "<td class='editiditm icon edit'><center><a href='$scriptname?action=editvoucher&amp;id=".$r['id']."'><img src='../images/edit2.png'></a><a href='../php/delvoucher.php?id=".$r['id']."'><img src='../images/delete.png' border=0></a></center></td>".
		"\n  <td>".$r['vouchernum']."</td>".
		"\n  <td><center>".$r['voucherstartdate']."</center></td>".
		"\n  <td><center>".(($r['vouchermins'] == "1440") ? "1 Day" : (($r['vouchermins'] == "4320") ? "3 Days" : (($r['vouchermins'] == "10080") ? "1 Week" : (($r['vouchermins'] == "44640") ? "1 Month" : (($r['vouchermins'] == "131040") ? "3 Months" : (($r['vouchermins'] == "393120") ? "9 Months" : (($r['vouchermins'] == "525600") ? "1 Year" : "")))))))."</center></td>".
		"\n  <td>".$r['voucheruser']."</td>".
		"\n  <td>".$r['voucherassigner']."</td>".
		"\n  <td>".$r['vouchernotes']."</td>";
}?>
</tbody>
</table>

</form>
</body>
</html>
