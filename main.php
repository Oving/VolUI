<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/VolUISite.css"/>		
	<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script> 
	<script type="text/javascript" src="js/bootstrap.min.js"></script>  
	<script type="text/javascript" src="js/lib/nu4.ds.min.js"></script>

	<title>NUCLEUS VolUI</title>
	<style type="text/css">
		h1
		{
			font-size: 2em;
  			-webkit-margin-before: 0.67em;
  			-webkit-margin-after: 0.67em;
  			-webkit-margin-start: 0px;
  			-webkit-margin-end: 0px;
  			font-weight: bold;
		}
	</style>
</head>
<body>
	<div> 
            <h1></h1><span id="h1span"></span>
    </div>

	<div id="container" class="container">
	<form>
		<table id="maintable" class="table table-hover table-condensed">
			<tbody>
				<tr >
					<th width="5%"></th>
  					<th>#</th>
  					<th>Device Name</th>
  					<th >Type</th>
  					<th>Volume</th>
  					<th>Line</th>
  				</tr>
  			</tbody>

		</table>
		<div class="form-group" style="float:left" align="left">
			<input id="checkall" type="checkbox" /> Check All&nbsp;&nbsp;
		</div>
		<div class="form-group" style="float:right" align="right">
			<button type="button" id="copyVolume" data-toggle="modal" onclick="preCopy(this.id)" data-target="#myModal">Copy Volume</button>
			<button type="button" id="copyLine" data-toggle="modal" onclick="preCopy(this.id)" data-target="#myModal">Copy Line</button>
		</div>

		<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">
        	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" onclick="Copy()" class="btn btn-primary">Copy</button>
      </div>
    </div>
  </div>
</div>

	</form>
	</div>

	<script type="text/javascript">

		var DeviceQueue = new Array();
		var currentDevice = 0;
		var siteid=null;
		var VorL=null;

			$('#checkall').click(function(){
				$('input[type="checkbox"]').each(function(){
					if($(this).attr("checked"))
						$(this).removeAttr("checked");
					else
					{
						$(this).attr("checked",true);
						$(this).prop("checked", true);
					}
				});
			});
	
		
		function EasytoClick(id)
		{

			if($('input[name='+id+']').attr("checked"))
					$('input[name='+id+']').removeAttr("checked");
				else
				{
					$('input[name='+id+']').attr("checked",true);
					$('input[name='+id+']').prop("checked", true);
				}
		}

		function judgeSite(deviceId)
		{
			var siteId = null;
			var arr = parent.sitesArr;
				for (var i = 0; i < arr.length; i++) {
					if(deviceId >= arr[i][0]   &&   deviceId <= arr[i][1])
					{
                    	siteId = i+1;
                    	break;
					}
				}
			return siteId;
		}

		function preCopy(VorL)
		{
			ClearpreCopy("div");
			if(VorL=='copyVolume')
				$('#myModalLabel').html('Copy Volume');
			if(VorL=='copyLine')
				$('#myModalLabel').html('Copy Line');

			this.VorL=VorL;
			DeviceQueue = new Array();
			currentDevice=0;

			$('#maintable').find('input[type="checkbox"]').each(function(){
				if($(this).attr("checked")=="checked")
				{
					currentDevice++;
					DeviceQueue.push($(this).attr("name"));
				}
					
			});
			
			var str = 	'<p>The number of devices waiting to duplicated: <b>'+ currentDevice +'</b></p> ' +
						'<p>Enter the ID you want to Copy from: ' +
						'<input id="copyid" type="text" oninput="ConfirmCopy()" placeholder="Device ID" aria-describedby="basic-addon1"></p>';



			$('.modal-body').append('<div>' + str + '</div>');
		}
		
		function ClearpreCopy(div)
		{
			$('.modal-body').find(div).remove();
			
		}
		function ConfirmCopy()
		{
			ClearpreCopy("#confirmInfo");
			var deviceId = $('#copyid').val();
			if(VorL=='copyLine')
			{
				siteid =judgeSite(deviceId);
				if(siteid)
				{
            	Nu4DataService.GetLineOfDevice(siteid,deviceId, function (line) {
            	if(line!=null)
				{
					
					line["color"] = line["color"].slice(0,-2);
					var str = 	'<div class="alert alert-success" role="alert">'+
								'<p>The relate information of Copy Line:</p>'+
            					'<p>Device Id:' + deviceId + '</p>' +
            					'<p>Color: ' + '<span style="background-color:#'+ line["color"] +';color:#'+ line["color"] +'">color</span>' + '</p>'+
            					'<table class="table table-bordered"><tr><th>X Value</th><th>Y Value</th><th>Z Value</th></tr>';
                				var vertices = line["vertices"];
                				for (var i = 0; i < vertices.length; i++) {
                    				var vertex = vertices[i];
                    				str += 	'<tr><td>' + vertex["x"] + '</td>' +
                    						'<td>' + vertex["y"] + '</td>' +
                        					'<td>' + vertex["z"] + '</td></tr>';
                				}
                				str += '</table></div>';
				}
				else
				{
					var str = '<div class="alert alert-danger" role="alert"><p>This ID is not belong to any devices which has line!</p></div>';
					
				}
				$('.modal-body').append('<div id="confirmInfo">' + str + '</div>');
            	});
				}
			}
			if(VorL=='copyVolume')
			{
				Nu4DataService.GetVolumeOfDevice(deviceId, function (volume) {
            	if(volume!=null)
				{
					
					volume["color"] = volume["color"].slice(0,-2);
					var str = 	'<div class="alert alert-success" role="alert">'+
								'<p>The relate information of Copy Volume:</p>'+
            					'<p>Device Id:' + deviceId + '</p>' +
            					'<p>Volume Height:' + volume["height"] + '</p>' +
            					'<p>Color: ' + '<span style="background-color:#'+ volume["color"] +';color:#'+ volume["color"] +'">color</span>' + '</p>'+
            					'<table class="table table-bordered"><tr><th>X Value</th><th>Z Value</th></tr>';
                				var vertices = volume["vertices"];
                				for (var i = 0; i < vertices.length; i++) {
                    				var vertex = vertices[i];
                    				str += 	'<tr><td>' + vertex["x"] + '</td>' +
                        					'<td>' + vertex["z"] + '</td></tr>';
                				}
                				str += '</table></div>';
				}
				else
				{
					var str = '<div class="alert alert-danger" role="alert"><p>This ID is not belong to any devices which has volume!</p></div>';
					
				}
				$('.modal-body').append('<div id="confirmInfo">' + str + '</div>');
            	});
			}
			
		}
		function Copy()
		{
			var deviceId = $('#copyid').val();
			if(currentDevice==0)
			{
				alert('There are not devices waiting to duplicated.');
				return;
			}
			if(deviceId!=null)
			{
				if(VorL=='copyVolume')
				{
			Nu4DataService.GetVolumeOfDevice(deviceId, function (volume) {
				if(volume!=null)
				{
				for(var i =0; i<DeviceQueue.length;i++)
				{
					deviceId = DeviceQueue[i];
					vertices = volume['vertices'];
					height   = volume['height'];
					y        = volume['y'];
					color    = volume['color']
				 	Nu4DataService.SetVolumeOfDevice(deviceId, vertices, height, y, color, function (saved) {
				 	});
				}
				alert("All Done!");
				window.location.href = "main.php";
				}
				else
					alert("This ID is not included volume!");
			});
				}
				if(VorL=='copyLine')
				{
					if(!siteid)
					alert('Please check the device ID.');
					else
					{
					Nu4DataService.GetLineOfDevice(siteid,deviceId, function (line) {
						if(line!=null)
						{
						for(var i =0; i<DeviceQueue.length;i++)
						{
							deviceId = DeviceQueue[i];
							vertices = line['vertices'];
							color    = line['color']
				 			Nu4DataService.SetLineOfDevice(deviceId, vertices, color, function (saved) {
				 			});
						}
						alert("All Done!");
						window.location.href = "main.php";
						}
						else
							alert("This ID is not included line!");
					});
					}
				}
			}
			else
				alert("Please enter the ID!");
			
		}
		
	</script>
</body>
</html>
