<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery.blockUI.js"></script>

<script>
	$(function() {
		$("#locations a").click(function() {
			var page = this.hash.substr(1);
			$.get("php/locations/"+page+".php",function(gotHtml) {
				$("#content").html(gotHtml);
				document.getElementById("content").target = "bldg";
			});
			return false;
		});
	});
</script>
<SCRIPT LANGUAGE="JavaScript"> 

			  function confirm_filled($row)
			  {
				  var filled = 0;
				  $row.find('input,select').each(function() {
					  if (jQuery(this).val()) filled++;
				  });
				  if (filled) return confirm('Do you really want to remove this row?');
				  return true;
			  };

			 $(document).ready(function() {

				//delete table row on image click
				$('.delrow').click(function(){
					var answer = confirm("Are you sure you want to delete this row ?")
					if (answer) 
				  $(this).parent().parent().remove();
				});

				$("#caddrow").click(function($e) {
				var row = $('#contactstable tr:last').clone(true);
					$e.preventDefault();
				row.find("input:text").val("");
				row.find("img").css("display","inline");
				row.insertAfter('#contactstable tr:last');
				});
				$("#uaddrow").click(function($e) {
				var row = $('#urlstable tr:last').clone(true);
					$e.preventDefault();
				row.find("input:text").val("");
				row.find("img").css("display","inline");
				row.insertAfter('#urlstable tr:last');
				});
			  });

			  $(document).ready(function() {
				$("#locationid").change(function() {
				  var locationid=$(this).val();
				  var locareaid=$('#locareaid').val();
				  var dataString = 'locationid='+ locationid;
				  
				  $.ajax ({
				  type: "POST",
				  url: "php/locarea_options_ajax.php",
				  data: dataString,
				  cache: false,
				  success: function(html) {
					$("#locareaid").html(html);
				  }
				  });
				});
			  });
</SCRIPT>

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
}
</style>
</head>


<body onload ="defContent;">
  <ul id="locations">
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/AEC.php'" style="cursor:pointer"><?php te("AEC");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/AG.php'" style="cursor:pointer"><?php te("AG");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/AH.php'" style="cursor:pointer"><?php te("AH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/BB.php'" style="cursor:pointer"><?php te("BB");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/CH.php'" style="cursor:pointer"><?php te("CH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/CT.php'" style="cursor:pointer"><?php te("CT");?></a></li>
    <!--<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/DGHH.php'" style="cursor:pointer"><?php te("DGHH");?></a></li>-->
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/DH.php'" style="cursor:pointer"><?php te("DH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/FL.php'" style="cursor:pointer"><?php te("FL");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/GA.php'" style="cursor:pointer"><?php te("GA");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/GB.php'" style="cursor:pointer"><?php te("GB");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/GC.php'" style="cursor:pointer"><?php te("GC");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/HH.php'" style="cursor:pointer"><?php te("HH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/HMH.php'" style="cursor:pointer"><?php te("HMH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/KWEC.php'" style="cursor:pointer"><?php te("KWEC");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/LFE.php'" style="cursor:pointer"><?php te("LFE");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/LFW.php'" style="cursor:pointer"><?php te("LFW");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/MA.php'" style="cursor:pointer"><?php te("MA");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/MC.php'" style="cursor:pointer"><?php te("MC");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/MH.php'" style="cursor:pointer"><?php te("MH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/MM.php'" style="cursor:pointer"><?php te("MM");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/MU.php'" style="cursor:pointer"><?php te("MU");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/OPP.php'" style="cursor:pointer"><?php te("OPP");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/PH.php'" style="cursor:pointer"><?php te("PH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/PRES.php'" style="cursor:pointer"><?php te("PRES");?></a></li>
    <br /><br />
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/RC.php'" style="cursor:pointer"><?php te("RC");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/RH.php'" style="cursor:pointer"><?php te("RH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SB.php'" style="cursor:pointer"><?php te("SB");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SCM.php'" style="cursor:pointer"><?php te("SCM");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SF.php'" style="cursor:pointer"><?php te("SF");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SFP.php'" style="cursor:pointer"><?php te("SFP");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SH.php'" style="cursor:pointer"><?php te("SH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SPA.php'" style="cursor:pointer"><?php te("SPA");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SPB.php'" style="cursor:pointer"><?php te("SPB");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SPC.php'" style="cursor:pointer"><?php te("SPC");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/SPD.php'" style="cursor:pointer"><?php te("SPD");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/STH.php'" style="cursor:pointer"><?php te("STH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/TF.php'" style="cursor:pointer"><?php te("TF");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/TH.php'" style="cursor:pointer"><?php te("TH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WH.php'" style="cursor:pointer"><?php te("WH");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WI.php'" style="cursor:pointer"><?php te("WI");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WP1.php'" style="cursor:pointer"><?php te("WP1");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WPA.php'" style="cursor:pointer"><?php te("WPA");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WPB.php'" style="cursor:pointer"><?php te("WPB");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WPC.php'" style="cursor:pointer"><?php te("WPC");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WPD.php'" style="cursor:pointer"><?php te("WPD");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WPE.php'" style="cursor:pointer"><?php te("WPE");?></a></li>
    <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a onClick="locations.location.href='../php/locations/WPF.php'" style="cursor:pointer"><?php te("WPF");?></a></li>
<div id="content_wrapper"><div id="content_wrapper">
	<div id="content" class="tab_content">
		<iframe src="../php/locations/UA.php" width="100%" height="890" name="locations" frameBorder="0"></iframe>
    </div>
</div>
</body>