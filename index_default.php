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
$allowed_host = 'video.conf.medvc.eu';
$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
if(substr($host, 0 - strlen($allowed_host)) !== $allowed_host) {
    die();
}

$isSafari = false;
if (stripos( $_SERVER['HTTP_USER_AGENT'], 'Chrome') === false && stripos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false)
{
    $isSafari = true;
}

if(!isset($_GET['x'])){
    die();
}

$_SESSION['pass'] = $_GET['x'];
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

<?php
if (!$isSafari) {
?>
    <!-- actual script modules -->
    <script type="module" crossorigin="anonymous">

        // RFB holds the API to connect and communicate with a VNC server
        import RFB from './core/rfb.js';

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

        document.getElementById('sendCtrlAltDelButton')
            .onclick = sendCtrlAltDel;

        function freezButtons() {
            document.getElementById('send_next_button').disabled = true;
            document.getElementById('send_prev_button').disabled = true;
            setTimeout(function(){
                document.getElementById('send_next_button').disabled = false;
                document.getElementById('send_prev_button').disabled = false;
            }, 3000);
        }

		function sendPrev() {
            rfb.sendKey(0xff51, 0);
            freezButtons();
        }

        function sendNext() {
            rfb.sendKey(0xff53, 0);
            freezButtons();
        }

        function openFullscreen() {
          let elem = document.body;
		  if (elem.requestFullscreen) {
			elem.requestFullscreen();
		  } else if (elem.mozRequestFullScreen) { /* Firefox */
			elem.mozRequestFullScreen();
		  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
			elem.webkitRequestFullscreen();
		  } else if (elem.msRequestFullscreen) { /* IE/Edge */
			elem.msRequestFullscreen();
		  }
        }

        function closeFullscreen() {
		  if (document.exitFullscreen) {
		    document.exitFullscreen();
		  } else if (document.mozCancelFullScreen) { /* Firefox */
		    document.mozCancelFullScreen();
		  } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
		    document.webkitExitFullscreen();
		  } else if (document.msExitFullscreen) { /* IE/Edge */
		    document.msExitFullscreen();
		  }
		}

        document.getElementById('novnc_iframe').addEventListener('fullscreenchange', (event) => {
		  let fullScreenButton = document.getElementById('full_screen_button');
		  if (document.fullscreenElement) {
		    fullScreenButton.innerText = "Close full screen";
		    fullScreenButton.onclick = closeFullscreen;
		  } else {
		    fullScreenButton.innerText = "Full screen";
		    fullScreenButton.onclick = openFullscreen;
		  }
		});

		document.getElementById('send_prev_button')
            .onclick = sendPrev;

		document.getElementById('send_next_button')
            .onclick = sendNext;

        document.getElementById('full_screen_button')
            .onclick = openFullscreen;

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


        //rfb.qualityLevel = 0;
        //rfb.compressionLevel = 9;

        // Add listeners to important events from the RFB module
        rfb.addEventListener("connect",  connectedToServer);
        rfb.addEventListener("disconnect", disconnectedFromServer);
        rfb.addEventListener("credentialsrequired", credentialsAreRequired);
        rfb.addEventListener("desktopname", updateDesktopName);

        // Set parameters that can be changed on an active connection
        rfb.viewOnly = readQueryVariable('view_only', false);
        rfb.scaleViewport = readQueryVariable('scale', true);
    </script>

<?php
}
?>
</head>

<body>
<?php
if ($isSafari) {
?>

 <div id="screen" style="text-align: center; background-color:white;">
  <p>The presentation is opened in a new tab.</p>
  <p>If the presentation did not open automatically, press the button below.</p>
  <button id="open_button" onclick="openPresentation()">Open presentation</button>
 <script type="text/javascript">
 	var newWin = window.open('<?php echo($_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . substr(explode('?', $_SERVER['REQUEST_URI'], 2)[0], 0, -4).'_safari.php');?>', '_blank');
 	if (newWin) newWin.focus();

	function openPresentation() {
		if (newWin) newWin.close();
		newWin = window.open('<?php echo($_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . substr(explode('?', $_SERVER['REQUEST_URI'], 2)[0], 0, -4).'_safari.php');?>', '_blank');
	}

	document.getElementById('open_button').onclick = openPresentation;

	document.body.onunload = function() {
		if (newWin) newWin.close();
	};


 </script>
  </div>

<?php
} else {
?>
    <div id="top_bar">
        <div id="status">Loading</div>
        <div id="sendCtrlAltDelButton">Send CtrlAltDel</div>
    </div>
	<span style="display: block; text-align: center;" ><button style="font-size: 100%; margin-right: 10px;" id="send_prev_button">Previous slide</button><button  style="font-size: 100%; margin-right: 10px;" id="send_next_button">Next slide</button><button style="font-size: 100%;" id="full_screen_button">Full screen</button></span>
    <div id="screen">
        <!-- This is where the remote screen will appear -->
    </div>
<?php
}
?>
</body>
</html>