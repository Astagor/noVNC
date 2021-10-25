<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$fontweight = '200';
if(isset($_REQUEST['font-weight'])) $fontweight = $_REQUEST['font-weight'];

$fontsize = '500';
if(isset($_REQUEST['font-size'])) $fontsize = $_REQUEST['font-size'];

$backgroundcolor = 'transparent';
if(isset($_REQUEST['background-color'])) $backgroundcolor = $_REQUEST['background-color'];
if($backgroundcolor !== 'transparent') $backgroundcolor = '#'.$backgroundcolor;

$colornormal = 'FFFFFF';
if(isset($_REQUEST['color-normal'])) $colornormal = $_REQUEST['color-normal'];

$colortwo = 'FFFF00';
if(isset($_REQUEST['color-two'])) $colortwo = $_REQUEST['color-two'];

$colorone = 'FFFF00';
if(isset($_REQUEST['color-one'])) $colorone = $_REQUEST['color-one'];

$colorzero = 'FF0000';
if(isset($_REQUEST['color-zero'])) $colorzero = $_REQUEST['color-zero'];

?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Timer</title>

<meta name="keywords" content="remote collaboration, remote consultation, healthcare, telemedicine, video, doctor, medical services, collaboration tools, videoconferencing, medvc, eHealth, mHealth, e-health, m-health">



    <meta charset="utf-8" />

<style>
body, html{
    height: 100%;
    font-family: "Fira Sans", "Source Sans Pro", Helvetica, Arial, sans-serif;
    font-weight: <?=$fontweight?>;
    font-size: <?=$fontsize?>%;
    background-color: <?=$backgroundcolor?>;
    overflow: hidden;
}

.normal {
    color: #<?=$colornormal?>;
}

.two {
    color: #<?=$colortwo?>;
}

.one {
    color: #<?=$colorone?>;
    animation: blinker2 2s linear infinite;
}

.zero {
    color: #<?=$colorzero?>;
    animation: blinker 0.5s linear infinite;
}

@keyframes blinker {
  50% { opacity: 10%; }
}

@keyframes blinker2 {
  50% { opacity: 25%; }
}


</style>


  </head>
  <body>
<div id="timer" class="normal">00:00</div>
   <script>

var timerStarted = false;
var timer_time = 0;
var timerInterval = null;
var startTime = 0;
var machineTimeOffset = 0;

    function processResponse(checkResponse) {

		var newObj = JSON.parse(checkResponse);

		var m = newObj['m'];
		var s = newObj['s'];

		var started = newObj['started'];
		var st = newObj['startTime'];

    	if (started) {
    		// start timer
			startTime = st;
			timer_time = m * 60 + s;
			if (timerInterval === null) {
				machineTimeOffset = Math.floor(Date.now() / 1000) - newObj['currentTime']; //set only once at start
    			timer();
    			clearInterval(timerInterval);
    	  	    timerInterval = window.setInterval(timer, 1000);
			}
    	} else {
    		// stop timer
    		clearInterval(timerInterval);
    		timerInterval = null;
			document.getElementById('timer').innerText = returnData(m) + ':' + returnData(s);
			document.getElementById('timer').className = 'normal';
    	}

    }

    function timer() {
    	var span = Math.floor(Date.now() / 1000) - machineTimeOffset - startTime - timer_time + 1;
		var isNegative = false;

		if (span < 0) {
			isNegative = true;
			span = Math.abs(span);
		}

		if (!isNegative) {
			document.getElementById('timer').className = 'zero';
		} else if (span < 60) {
			document.getElementById('timer').className = 'one';
		} else if (span < 2*60) {
			document.getElementById('timer').className = 'two';
		} else {
			document.getElementById('timer').className = 'normal';
		}


		var m = Math.floor(span / 60);
		var s = span % 60;

    	document.getElementById('timer').innerText = (isNegative?'':'-') + returnData(m) + ':' + returnData(s);
    }

    	function returnData(input) {

    	  return input < 10 ? '0'+input : ''+input;
    	}

    function getTimer(){
		var xhttp = new XMLHttpRequest();
		  xhttp.onreadystatechange = function() {
		      if (this.readyState == 4) {
		      	if (this.status == 200) {
			      	var checkResponse = xhttp.responseText;
		      		if (checkResponse !== '') {
		      			processResponse(checkResponse);
		      		}
		      	}
		      }
		  };

		  xhttp.open("GET", "timer_api.php?action=get", true);
		  xhttp.send();
	}

    getTimer();

    var intervalId = window.setInterval(getTimer, 3000);

    </script>
  </body>
</html>