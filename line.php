<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css"/>
	<link rel='stylesheet' href='css/spectrum.css' />
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/lib/nu4.ds.min.js"></script>
	<script src="js/jquery.tablednd.js"></script>
	<script src='js/spectrum.js'></script>
	

	<title>NUCLEUS VolUI</title>
	<style type="text/css">
		body
		{
			margin-top:20px;
		}
		input[name="vertices"]
		{
			width:90%;
			height:30px;
		}
		.highlight
		{
			background-color: #da5353;
		}
		#ReturnButton
		{
			cursor:pointer;
			text-decoration: underline;
			float: right;
			margin-top: -8px;
			padding: 8px 10px;
		}
		#ReturnButton:hover
		{
			background-color: #fff;
			color:#000;
			text-decoration: none;
		}
	</style>


</head>
<body>
	<div class="container">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Create a new line： <strong><span id="idtext"></span></strong>
				<span id="ReturnButton" onclick='javascript:window.location.href = "main.php"' >Return devices list</span>
				</h3>
			</div>
			<div  class="panel-body">

			
				<table id="maintable" class="table table-condensed">
					<tr class='nodrop nodrag'>
						<td ><h5>Choose a Color：</h5></td>
						<td >
								<input type='text' id="custom" />
						</td>
						<td colspan="2" rowspan="7" style="color:#000;background-color:#cccccc" width="46%" align="center">
						<canvas id="myCanvas" width="500px" height="300px" >
							您的浏览器不支持canvas标签。
						</canvas></td>
					</tr>

					<tr class='nodrop nodrag'>
						<td colspan="2"><h5>Add a Vertex：<em>  Formats as: (X,Y,Z) or X,Y,Z    </em><a style="font-size: 120%; cursor:pointer;" onclick="addvertices();dragItems()"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></h5>
						</td> 
					</tr>
					<tr draggable="true">
						<td><label>vertices1：</label></td>
						<td>
							<input name="vertices" type="text">
							<a style="font-size: 120%; cursor:pointer;"  onclick="Removevertices(this)"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
						</td>
					</tr>
					<tr>
						<td><label>vertices2：</label></td>
						<td>
								<input name="vertices" type="text">
								<a style="font-size: 120%; cursor:pointer;"  onclick="Removevertices(this)"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
						</td>
					</tr>
					<tr>
						<td><label>vertices3：</label></td>
						<td>
								<input name="vertices" type="text">
								<a style="font-size: 120%; cursor:pointer;"  onclick="Removevertices(this)"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
						</td>
					</tr>
					
					<tr class='nodrop nodrag'>
						<td colspan="4"><button onclick="saveLine()" class="btn btn-primary">Save</button>
						&nbsp;&nbsp;
						<button onclick="cleanAll()" class="btn btn-primary">Clean</button>
						&nbsp;&nbsp;
						<button onclick="deleteLine()" class="btn btn btn-danger">Delete this line</button>
						</td>
					</tr>
				</table>
				
			</div>
		</div>



	</div>


		<script type="text/javascript">
	var currentNum=4;
	var deviceId = getUrlParam('id');  
	var siteId = getUrlParam('siteid');  
	$(document).ready(function(){
		//console.log(getUrlParam('id'));
		reSetColor("#000");
		showLine();  
		dragItems();
		$('input[name="vertices"]').keyup(function(){
			drawCanvas();
		});
	});

	function drawCanvas()
	{
		var vertices = [];
		$('input[name="vertices"]').each(function(){
			var InputVer = $(this).val();
	        var match = /[\(\)\s]/g;//delete the 'space'  '('   ')'
	        var ArryVer = InputVer.replace(match,"").split(",");
	        vertices.push({ 'x': (+ArryVer[0]+100), 'y': (+ArryVer[1]), 'z': (+ArryVer[2])});
		});

		/*--------------------------------------------------*/
		var min_X  = vertices[0]['x'];
		var min_Z  = vertices[0]['z'];
		var max_X  = min_X;
		var max_Z  = min_Z;
		for(var i =1; i<vertices.length ; i++)
		{
			if(vertices[i]['x'] < min_X)
				min_X = vertices[i]['x'];
			if(vertices[i]['z'] < min_Z)
				min_Z = vertices[i]['z'];
			if(vertices[i]['x'] > max_X)
				max_X = vertices[i]['x'];
			if(vertices[i]['z'] > max_Z)
				max_Z = vertices[i]['z'];
		}
		//console.log('min_X:'+min_X+' min_Z:'+min_Z+' max_X:'+max_X+' max_Z:'+max_Z);
		var m_width = max_X - min_X;
		var m_height = max_Z - min_Z;
		var CANVAS_WIDTH = 450;
		var CANVAS_HEIGHT = 250;
		var scale=0.0;
		if(m_height>m_width/* && m_height>CANVAS_HEIGHT*/)
		{
			scale = CANVAS_HEIGHT / m_height;
		}
		if(m_width>=m_height/* && m_width>CANVAS_WIDTH*/)
		{
			scale = CANVAS_WIDTH / m_width;
			if (m_height*scale>CANVAS_HEIGHT) 
				scale *= CANVAS_HEIGHT / (m_height*scale);

		}

		//console.log('H>W:'+(m_height>m_width)+'  scale:'+scale)
		for(var i =0; i<vertices.length ; i++)
		{
		    vertices[i]['x'] *= scale;
		    vertices[i]['z'] *= scale;
			//console.log(vertices[i]['x']+' , '+vertices[i]['z'])
		}	
		min_X *= scale;
		min_Z *= scale;

		var transX = 50 - min_X;
		var transZ = 50 - min_Z;
		//console.log('transX:'+transX+'  transZ:'+transZ )
		for(var i =0; i<vertices.length ; i++)
		{
			//console.log(vertices[i]['x']+','+vertices[i]['z'])
			vertices[i]['x'] += transX;
			vertices[i]['z'] += transZ;
			//console.log(vertices[i]['x']+','+vertices[i]['z']+'---------')
		}

		/*--------------------------------------------------*/
		var canvas = document.getElementById("myCanvas");
	    var ctx = canvas.getContext("2d");  
	    ctx.clearRect(0,0,canvas.width,canvas.height);
	    ctx.lineWidth=3.0;
	    ctx.beginPath();

	    var color = $('#custom').spectrum("get").toHex();
		color = '#' + color;
	    ctx.strokeStyle = color;
	    //ctx.fillStyle = color;

	    if(vertices.length<2)
		{
			ctx.fillStyle = '#ff0000';
	    	ctx.font = "20px Arial";
	    	ctx.fillText('failed: Can not constitute a line.',50,100);

		}

	    ctx.moveTo(vertices[0]['x'], vertices[0]['z']);
	    for(var i =1; i<vertices.length ; i++)
	    {
	    	if(vertices[i]['x']==""  ||  vertices[i]['z']==""  || isNaN(vertices[i]['x']) ||  isNaN(vertices[i]['z']))
	    	{
	    		ctx.fillStyle = '#ff0000';
	    		ctx.font = "30px Arial";
	    		ctx.fillText('Wrong vertices.',150,100);
	    		return;
	    	}
	    	ctx.lineTo(vertices[i]['x'], vertices[i]['z']);
	    }
	    //console.log(vertices[0]['x']);
	    //ctx.closePath();
	    ctx.stroke();

	    //ctx.fill();
	}

	function dragItems()
	{
		$("#maintable").tableDnD({  
    		onDragClass:'highlight',
    		onDrop:function()
    		{
    			drawCanvas();
    		}

		});
	}


	function getUrlParam(name){  
    //构造一个含有目标参数的正则表达式对象  
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");  
    //匹配目标参数  
    var r = window.location.search.substr(1).match(reg);  
    //返回参数值  
    if (r!=null) return unescape(r[2]);  
    return null;  
	}  

	function addvertices()
	{
		var str = '<td><label>vertices' + (currentNum++) +'：</label></td>' +
				  '<td><input name="vertices" onkeyup="drawCanvas()" type="text"> ' +
				  '<a style="font-size: 120%; cursor:pointer;"  onclick="Removevertices(this)"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>'+
				  '</td>';
		$('#maintable').find('tr:eq(-2)').after('<tr>' + str + '</tr>');
	}
	function Removevertices(thisV)
	{
		//$(thisV).parent().parent().remove();
		
		var $arr = $(thisV).parent().parent().nextAll().find('input');
		$(thisV).prev().val($arr.eq(0).val());
		for(var i=0;i<$arr.length-1;i++)
			$arr.eq(i).val($arr.eq(i+1).val());
		$('#maintable').find('tr:eq(-2)').remove();
		currentNum--;
		drawCanvas();
	}

	function showLine() {        
			
			$('#idtext').html("ID:("+deviceId+")");
            Nu4DataService.GetLineOfDevice(siteId,deviceId, function (Line) {
            if(Line!=null)
            {
				Line["color"] = Line["color"].substr(-2) + Line["color"].slice(0,-2) ;
                reSetColor(Line["color"]);

                var maintable = $('#maintable');
                var vertices = Line["vertices"];
                for (var i = 3; i < vertices.length; i++) {
                	addvertices();
                }
                for (var i = 0; i < vertices.length; i++) {
                    var vertex = vertices[i];
                    maintable.find('input:eq('+(1+i)+')').val('('+vertex["x"]+','+vertex["y"]+','+vertex["z"]+')');
                }
            }
            else
            	$('.btn-danger').remove();
        });
            drawCanvas();
        };

        function deleteLine(){
        	Nu4DataService.DeleteLineOfDevice(deviceId, function (deleted) {});
        	alert("Hava Deleted!");
        	window.location.href = "main.php";
        }

        function saveLine() {
            var sign = 0;
            var color = $('#custom').spectrum("get").toHex8();
            color = color.slice(2) + color.slice(0,2);
            if (isNaN(deviceId) || color.length != 8) 
            {
            	alert("Failed: Please check the necessary parameters again!");
            	return;
            }
            var vertices = [];
            var tableLine = $('#maintable');
            var vNum = tableLine.find('input[name="vertices"]');
            vNum.each(function () {
                var InputVer = $(this).val();
                var match1 = /[\(\)\s]/g;//delete the 'space'  '('   ')'
                var ArryVer = InputVer.replace(match1,"").split(",");
                if(InputVer=="")
                {
                	sign = 1;
                	return true;//continue
                }
				if(isNaN(ArryVer[0]) || isNaN(ArryVer[1]) || isNaN(ArryVer[2]) || ArryVer[0]==""|| ArryVer[1]=="" || ArryVer[2]=="" || ArryVer.length!=3)
                {
                	return false;//break
                }
                if(sign==1)
                {
                	sign=2;
                	return false;//break
                }
               	vertices.push({ 'x': ArryVer[0], 'y': ArryVer[1], 'z': ArryVer[2] });
            });

            if (vertices.length == 0 || vertices.length<2 || (sign!=1 && (vertices.length!=vNum.length) ) )
            {
            	alert("Failed: Please check the vertices again.");
            	return;
            }
            Nu4DataService.SetLineOfDevice(deviceId, vertices, color, function (saved) {
            	alert("Success: Hava Saved!");
                window.location.href = "main.php";

            });
        };

        function cleanAll()
        {
        	$('input[type=text]').val("");
        	drawCanvas();
        }

        function reSetColor(CurrentColor)
        {
        	$("#custom").spectrum({
		    preferredFormat: "hex8",    		
		    color: CurrentColor,
		    showInput: true,
		    className: "full-spectrum",
		    showInitial: true,
		    showAlpha: true,
		    showPalette: true,
		    showSelectionPalette: true,
		    maxPaletteSize: 10,
		    localStorageKey: "spectrum.demo",
		    move: function (color) {
		        
		    },
		    show: function () {
		    
		    },
		    beforeShow: function () {
		    
		    },
		    hide: function () {
		    
		    },
		    change: function() {
		        drawCanvas();
		    },
		    palette: [
		        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
		        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
		        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
		        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
		        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
		        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
		        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
		        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
		        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
		        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
		        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
		        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
		        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
		        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
		    		]
			});
        }

    
	</script>
</body>
</html>
