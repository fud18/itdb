<SCRIPT LANGUAGE="JavaScript"> 
  $(document).ready(function() {
    $("#tabs").tabs();
    $("#tabs").show();
  });
</script>

<h1>Import Records</h1>

<div id="tabs"><!-- tab container -->
  <ul>
    <li style="display:none"><a href="../php/importwelcome.php" target="import"><?php te("Start Here");?></a></li>
    <li style="width:10em"><a href="../php/importagents.php" target="import"><?php te("Agents");?></a></li>
    <li style="width:10em"><a href="../php/importcontracts.php" target="import" style="cursor:pointer"><?php te("Contracts");?></a></li>
    <li style="width:10em"><a href="../php/importdepartments.php" target="import" style="cursor:pointer"><?php te("Departments");?></a></li>
    <li style="width:10em"><a href="../php/importfiber.php" target="import" style="cursor:pointer"><?php te("Fiber");?></a></li>
    <li style="width:10em"><a href="../php/importvouchers.php" target="import" style="cursor:pointer"><?php te("Guest Vouchers");?></a></li>
    <li style="width:10em"><a href="../php/importinvoices.php" target="import" style="cursor:pointer"><?php te("Invoices");?></a></li>
    <li style="width:10em"><a href="../php/importitems.php" target="import" style="cursor:pointer"><?php te("Items");?></a></li>
    <li style="width:10em"><a href="../php/importjacks.php" target="import" style="cursor:pointer"><?php te("Jacks");?></a></li>
    <li style="width:10em"><a href="../php/importlocations.php" target="import" style="cursor:pointer"><?php te("Locations");?></a></li>
    <li style="width:10em"><a href="../php/importlocareas.php" target="import" style="cursor:pointer"><?php te("Areas/Rooms");?></a></li>
    <li style="width:10em"><a href="../php/importsoftware.php" target="import" style="cursor:pointer"><?php te("Software");?></a></li>
    <li style="width:10em"><a href="../php/importvlans.php" target="import" style="cursor:pointer"><?php te("VLANS");?></a></li>
  </ul>
</div><!-- tab container -->
	<iframe style="height:850px" width="100%" id="import" name="import" frameborder="0"></iframe>