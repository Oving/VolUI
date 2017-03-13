<html>
	<head>
		<title>NUCLEUS VolUI</title>
		<link rel="stylesheet" type="text/css" href="css/VolUI.css"/>	
		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="js/lib/nu4.ds.min.js"></script>
		<script type="text/javascript" src="js/FileSaver.js"></script>

		 <script>

         var sitesArr= [];
         var siteNum=0;


            function triggerLogin(event) {
                if (event.keyCode == 13) {
                    login();
                }
            };

            function login() {
                Nu4DataService.Login($("#textUser").val(), $("#textPassword").val(), function (success) {
                    if(success)
                        location.reload();
                    else
                        $("#siteList").html("<div style='color:red'>Worng password or username.</div>");
                });
            };

            function logout() {
                Nu4DataService.Logout(function () {
                                $("#siteList").html("<div style='color:red'>Please login at bottom-left</div>");
                                $("#textUser").val("");
                                $("#textPassword").val("");
                                location.reload();
                            });
            };

        	function listSites() {
            Nu4DataService.GetAllSites(function (sites) {
            	siteNum=sites.length;

                for(var i=0;i<siteNum;i++)
                {
                    Nu4DataService.GetAllDevicesOfSite(i+1, function (devices) {
                        if(devices[0])
                        {
                            sitesArr[i] = [devices[0]['id'],devices[devices.length-1]['id']];
                        }
                    });
                }
               

                var SitesNav = $('#siteList');
                for (var i = 0; i < sites.length; i++) {
                    var site = sites[i];
                    var sitenames = site["id"] + ': ' + site["name"];
                    if(sitenames.length>20)
                        sitenames=sitenames.substr(0,20)+"...";
                    var str = '<div id="siteItem'+ site["id"] +'" class="siteItem" data-siteId="' + site["id"] + '" data-siteName="' + site["name"] + '"><span class="siteText">' 
                    + sitenames + '</span></div>';
                    SitesNav.append(str);
                }
                registerSiteItemEvent(sites);
            });
        };

        function registerSiteItemEvent(sites){
                var main = $('#mainFrame');
                for (var i = 0; i < sites.length; i++) 
                {
                    var site = sites[i];
                    var siteText = $("#siteItem"+site["id"]);
                    siteText.click(function(){
                        main.attr("src", "main.php");
                        var siteName = $(this).data("sitename");
                        var siteId = $(this).data("siteid");   
                        main.load(function(){
                            listDevices(siteId);
                            main.contents().find('h1').html(siteName);  
                            main.contents().find('#h1span').html("&nbsp;(ID: " + siteId +")");

                        }); 
                    });     
                }
        }

        	function listDevices(siteId) {
        
            Nu4DataService.GetAllDevicesOfSite(siteId, function (devices) {            
                clearDevices();
                var tableDevices = $('#mainFrame').contents().find("#container"); 
                //var str = '<h3>PointLocationType:</h3>';
                for (var i = 0; i < devices.length; i++) {
                    var device = devices[i];
                    var str = '<td>' + '<input name="'+device["id"]+'" type="checkbox" value="" />' + '</td>' +
                    	'<td>' + device["id"] + '</td>' +
                        '<td>' + device["name"] + '</td>' +
                        '<td>' + device["type"] + '</td>' +
                        '<td><a href="volume.php?id='+ device["id"] +'">'; 
                        str += (device["hasVolume"])?'<strong>Edit</strong>':'New';
                        str += '</a></td><td><a href="Line.php?id='+ device["id"] +'&siteid='+ siteId +'">'; 
                        str += (device["hasLine"])?'<strong>Edit</strong>':'New';
                        str += '</a></td>';
                    tableDevices.find('tr:last').after('<tr style="cursor:pointer" onclick="EasytoClick('+device["id"]+')">' + str + '</tr>');
                }

            });
        };
        	function clearDevices() {
            var tableDevices = $('#mainFrame').contents().find("#container"); 
            tableDevices.find('tr:gt(0)').remove();
        };

        function initializeUser() {
    
            //var _interval;
             var siteList = $("#siteList");
             Nu4DataService.IsGuest(function (isGuest) {
                    if (isGuest) {
                        siteList.html("<div style='color:red'>Please login at bottom-left</div>");
                        $("#textUser").prop('disabled', false);
                        $("#textPassword").prop('disabled', false);
                        $("#login").html("Login");
                        $("#login").off("click");
                        $("#login").on("click",login);
                    }
                    else {
                        $("#textUser").prop('disabled', true);
                        $("#textPassword").prop('disabled', true);
                        $("#login").html("Logout");
                        $("#login").off("click");
                        $("#login").on("click",logout);
                    }});
    
        };
        $(function () {
            initializeUser();

            $('#home').click(function(){
        		location.reload();
        	});
        });

        

        </script>
	</head> 
	<body onload="listSites()">
        <div id="leftBar">
            <div id="home">NUCLEUS VolUI <span style="font-size:18px;">(Manual)</span></div>
            <div id="siteList">

            </div>
            <div class="toolBar">
                <span>ID: </span> <input id="textUser" type="text" style="width:60px;"/>
                <span>Password: </span> <input id="textPassword" type="password" style="width:65px;" onkeypress="triggerLogin(event)"/>
                <button id="login" >Login</button>
                
             </div>
        </div>
        <div id="containerFrame">
            <iframe id="mainFrame" src="document.html"></iframe>
        </div>

       
		
	</body>
</html>
