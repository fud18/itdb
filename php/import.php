<SCRIPT LANGUAGE="JavaScript"> 
  $(document).ready(function() {
    $("#tabs").tabs();
    $("#tabs").show();
  });

$(document).ready(function() {
$(".tab").click(function () {
    $(".tab").removeClass("ui-tabs-selected ui-state-active");
    $(this).addClass("ui-state-default ui-corner-top ui-tabs-selected ui-state-active");   
});
});
</script>

<link href="../css/jquery-themes/blue2/jquery-ui-1.8.12.custom.css" rel="stylesheet" type="text/css" />

<style type="text/css">
ul {
  margin:0px;
  padding:0px;
  overflow:hidden;
}

li {
  float:left;
  list-style:none;
  padding:8px;
  width:10em;
}

a {
	width:80%;
}
</style>

<h1>Import Records</h1>
<div>
  <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all"> 
    <li style="display:none"><a href="../php/importwelcome.php" target="import" style="cursor:pointer"><?php te("Start Here");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importagents.php" target="import" style="cursor:pointer"><?php te("Agents");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importcontracts.php" target="import" style="cursor:pointer"><?php te("Contracts");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importdepartments.php" target="import" style="cursor:pointer"><?php te("Departments");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importfiber.php" target="import" style="cursor:pointer"><?php te("Fiber");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importvouchers.php" target="import" style="cursor:pointer"><?php te("Guest Vouchers");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importinvoices.php" target="import" style="cursor:pointer"><?php te("Invoices");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importitems.php" target="import" style="cursor:pointer"><?php te("Items");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importjacks.php" target="import" style="cursor:pointer"><?php te("Jacks");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importlocations.php" target="import" style="cursor:pointer"><?php te("Locations");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importlocareas.php" target="import" style="cursor:pointer"><?php te("Rooms");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importsoftware.php" target="import" style="cursor:pointer"><?php te("Software");?></a></li>
    <li class="ui-state-default ui-corner-top tab"><a href="../php/importvlans.php" target="import" style="cursor:pointer"><?php te("VLANS");?></a></li>
  </ul> 
	<div id="content" class="x-hide-display">
		<iframe style="height:850px" width="100%" id="import" name="import" frameborder="0" src="./php/importwelcome.php"></iframe>
	</div>
</div>