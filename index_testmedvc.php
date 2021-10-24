<?php
session_set_cookie_params([
    'samesite' => 'lax'
]);
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
            text-align: center;
        }

    </style>

<?php
if (!$isSafari) {
?>
     <script type="module" crossorigin="anonymous">

        var slidesCount = 5;
        var currentSlide = 1;

		var hostArray = window.location.hostname.split('.');
		document.domain = hostArray[hostArray.length - 2] + '.' + hostArray[hostArray.length - 1];

        function freezButtons() {
            document.getElementById('send_next_button').disabled = true;
            document.getElementById('send_prev_button').disabled = true;
            setTimeout(function(){
                document.getElementById('send_next_button').disabled = false;
                document.getElementById('send_prev_button').disabled = false;
            }, 1000);
        }


        function showSlide() {
            document.getElementById('screen').innerHTML='<img id="fake_presentation" style="max-width:100%; max-height:100%;" src="/img/medvc/test_'+currentSlide+'.jpg"></img>';
		    document.getElementById('fake_presentation')
                .onclick = sendNext;
        }

		function sendPrev() {
            currentSlide = currentSlide - 1;
            if (currentSlide < 1) currentSlide = slidesCount;
            showSlide();
            freezButtons();
        }

        function sendNext() {
            currentSlide = currentSlide + 1;
            if (currentSlide > slidesCount) currentSlide = 1;
            showSlide();
            freezButtons();
        }

        function openFullscreen() {
          let elem = parent.document.getElementById('novnc_iframe');
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
		  if (parent.document.exitFullscreen) {
		    parent.document.exitFullscreen();
		  } else if (parent.document.mozCancelFullScreen) { /* Firefox */
		    parent.document.mozCancelFullScreen();
		  } else if (parent.document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
		    parent.document.webkitExitFullscreen();
		  } else if (parent.document.msExitFullscreen) { /* IE/Edge */
		    parent.document.msExitFullscreen();
		  }
		}

        parent.document.getElementById('novnc_iframe').addEventListener('fullscreenchange', (event) => {
		  let fullScreenButton = document.getElementById('full_screen_button');
		  if (parent.document.fullscreenElement) {
		    fullScreenButton.innerText = "Close full screen";
		    fullScreenButton.onclick = closeFullscreen;
		  } else {
		    fullScreenButton.innerText = "Open full screen";
		    fullScreenButton.onclick = openFullscreen;
		  }
		});

		document.getElementById('send_prev_button')
            .onclick = sendPrev;

		document.getElementById('send_next_button')
            .onclick = sendNext;

		document.getElementById('fake_presentation')
            .onclick = sendNext;

        document.getElementById('full_screen_button')
            .onclick = openFullscreen;


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
        <div id="status">Connected to TEST</div>
    </div>
	<span style="display: block; text-align: center;" ><button style="font-size: 100%; margin-right: 10px;" id="send_prev_button">Previous slide</button><button  style="font-size: 100%; margin-right: 10px;" id="send_next_button">Next slide</button><button style="font-size: 100%;" id="full_screen_button">Full screen</button></span>
    <div id="screen">
        <img id="fake_presentation" style="max-width:100%; max-height:100%;" src="/img/medvc/test_1.jpg"></img>
    </div>
  
<?php
}
?>
</body>
</html>