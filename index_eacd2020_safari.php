<?php
$maxlifetime = 4 * 24 * 60 * 60;
$samesite = 'none';
$secure = true;
$httponly = true;
if(PHP_VERSION_ID < 70300) {
    session_set_cookie_params($maxlifetime, '/; samesite='.$samesite, $_SERVER['HTTP_HOST'], $secure, $httponly);
} else {
    session_set_cookie_params([
        'lifetime' => $maxlifetime,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);
}
session_start();
//$allowed_host = 'video.conf.medvc.eu';
//$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
//if(substr($host, 0 - strlen($allowed_host)) !== $allowed_host) {
//    die('');
//}

if(isset($_GET['x'])){
    $_SESSION['pass'] = $_GET['x'];
}



if(!isset($_SESSION['pass'])){
    die('');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>medVC remote presenter</title>

    <meta charset="utf-8" />


    <style>

        body {
            margin: 0;
            background-color: transparent;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        html {
            height: 100%;
        }

        #top_bar {
            background-color: #6e84a3;
            color: white;
            font: bold 12px Helvetica;
            padding: 6px 5px 4px 5px;
            border-bottom: 1px outset;
        }
        #status {
            text-align: center;
        }
        #sendCtrlAltDelButton {
            position: fixed;
            top: 0px;
            right: 0px;
            border: 1px outset;
            padding: 5px 5px 4px 5px;
            cursor: pointer;
            display: none;
        }

        #screen {
            flex: 1; /* fill remaining space */
            overflow: hidden;
        }

    </style>

    <!-- actual script modules -->
    <script type="module" crossorigin="anonymous">

		document.domain = "medvc.eu";

        // RFB holds the API to connect and communicate with a VNC server
        import RFB from './core/rfb_video.js';

        let rfb;
        let desktopName;

        // When this function is called we have
        // successfully connected to a server
        function connectedToServer(e) {
            // status("Connected to " + desktopName);
            status("PRESENTATION CONTROL WINDOW");
        }

        // This function is called when we are disconnected
        function disconnectedFromServer(e) {
            if (e.detail.clean) {
                status("Disconnected");
            } else {
                status("Something went wrong, connection is closed");
            }
        }

        // When this function is called, the server requires
        // credentials to authenticate
        function credentialsAreRequired(e) {
            //const password = prompt("Password Required:");
            //rfb.sendCredentials({ password: password });
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
      	          if (this.status == 200) {
        	         var checkResponse = xhttp.responseText.replace(/\s/g,'');
        	         rfb.sendCredentials({ password: checkResponse });
                  }
                }
           };
           xhttp.open("GET", "auth.php", true);
           xhttp.send();
        }

        // When this function is called we have received
        // a desktop name from the server
        function updateDesktopName(e) {
            desktopName = e.detail.name;
        }

        // Since most operating systems will catch Ctrl+Alt+Del
        // before they get a chance to be intercepted by the browser,
        // we provide a way to emulate this key sequence.
        function sendCtrlAltDel() {
            rfb.sendCtrlAltDel();
            return false;
        }

        // Show a status text in the top bar
        function status(text) {
            document.getElementById('status').textContent = text;
        }

        // This function extracts the value of one variable from the
        // query string. If the variable isn't defined in the URL
        // it returns the default value instead.
        function readQueryVariable(name, defaultValue) {
            // A URL with a query parameter can look like this:
            // https://www.example.com?myqueryparam=myvalue
            //
            // Note that we use location.href instead of location.search
            // because Firefox < 53 has a bug w.r.t location.search
            const re = new RegExp('.*[?&]' + name + '=([^&#]*)'),
                  match = document.location.href.match(re);

            if (match) {
                // We have to decode the URL since want the cleartext value
                return decodeURIComponent(match[1]);
            }

            return defaultValue;
        }



		function sendPrev() {
            rfb.sendKey(0xff51, 0);
        }

        function sendNext() {
            rfb.sendKey(0xff53, 0);
        }

		function sendSpace() {
			rfb.sendKey(0xff80, 0);
		}


        function openFullscreen() {
		  if (document.requestFullscreen) {
			document.requestFullscreen();
		  } else if (document.mozRequestFullScreen) { /* Firefox */
			document.mozRequestFullScreen();
		  } else if (document.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
			document.webkitRequestFullscreen();
		  } else if (document.msRequestFullscreen) { /* IE/Edge */
			document.msRequestFullscreen();
		  }
        }





		document.getElementById('send_prev_button')
            .onclick = sendPrev;

		document.getElementById('send_next_button')
            .onclick = sendNext;

		document.getElementById('send_space_button')
            .onclick = sendSpace;

        // Read parameters specified in the URL query string
        // By default, use the host and port of server that served this file
        const host = readQueryVariable('host', window.location.hostname);
        let port = readQueryVariable('port', window.location.port);
        //const password = readQueryVariable('password');
        const path = readQueryVariable('path', 'websockify_novnc_medvc');

        // | | |         | | |
        // | | | Connect | | |
        // v v v         v v v

        status("Connecting");

        // Build the websocket URL used to connect
        let url;
        if (window.location.protocol === "https:") {
            url = 'wss';
        } else {
            url = 'ws';
        }
        url += '://' + host;
        if(port) {
            url += ':' + port;
        }
        url += '/' + path;

        // Creating a new RFB object will start a new connection
        //rfb = new RFB(document.getElementById('screen'), url,
        //              { credentials: { password: password } });

        rfb = new RFB(document.getElementById('screen'), url);


        rfb.qualityLevel = 1;
        rfb.compressionLevel = 9;

        // Add listeners to important events from the RFB module
        rfb.addEventListener("connect",  connectedToServer);
        rfb.addEventListener("disconnect", disconnectedFromServer);
        rfb.addEventListener("credentialsrequired", credentialsAreRequired);
        rfb.addEventListener("desktopname", updateDesktopName);

        // Set parameters that can be changed on an active connection
        rfb.viewOnly = readQueryVariable('view_only', false);
        rfb.scaleViewport = readQueryVariable('scale', true);
    </script>


</head>

<body>
    <div id="top_bar">
        <div id="status">Loading</div>
        <div id="sendCtrlAltDelButton">Send CtrlAltDel</div>
    </div>
	<span style="display: block; text-align: center;" ><button style="font-size: 100%; margin-right: 10px;" id="send_space_button">Play/Pause</button><button style="font-size: 100%; margin-right: 10px;" id="send_prev_button">-10s./Prev. slide</button><button  style="font-size: 100%;" id="send_next_button">+10s./Next slide</button></span>
    <div id="screen">
        <!-- This is where the remote screen will appear -->
    </div>
</body>
</html>