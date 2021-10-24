<?php
session_set_cookie_params([
    'samesite' => 'lax'
]);
session_start();
//$allowed_host = 'video.conf.medvc.eu';
//$host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
//if(substr($host, 0 - strlen($allowed_host)) !== $allowed_host) {
//    die('');
//}

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
            text-align: center;
        }

    </style>

    <!-- actual script modules -->
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
            document.getElementById('screen').innerHTML='<img id="fake_presentation" style="max-width:100%; max-height:100%;" src="/img/test_'+currentSlide+'.png"></img>';
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



		document.getElementById('send_prev_button')
            .onclick = sendPrev;

		document.getElementById('send_next_button')
            .onclick = sendNext;

		document.getElementById('fake_presentation')
            .onclick = sendNext;

    </script>


</head>

<body>
    <div id="top_bar">
        <div id="status">Connected to TEST</div>

    </div>
	<span style="display: block; text-align: center;" ><button style="font-size: 100%; margin-right: 10px;" id="send_prev_button">Previous slide</button><button style="font-size: 100%;" id="send_next_button">Next slide</button></span>
    <div id="screen">
        <img id="fake_presentation" style="max-width:100%; max-height:100%;" src="/img/test_1.png"></img>
    </div>
</body>
</html>