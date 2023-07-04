<?php
require_once('../auth_include.php');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Timer ADMIN</title>

<meta name="keywords" content="remote collaboration, remote consultation, healthcare, telemedicine, video, doctor, medical services, collaboration tools, videoconferencing, medvc, eHealth, mHealth, e-health, m-health">



    <meta charset="utf-8" />

<style>
body, html{
    height: 100%;
    font-family: "Fira Sans", "Source Sans Pro", Helvetica, Arial, sans-serif;
    font-weight: 400;
}
#new_question {
 width: 600px;
}

#questions {
  width: 100%;
  background-color: #007700;
  border: 1px solid #0000ff;

}

.question_even {
  margin-top: 0;
  background-color: #007700;
  border: 1px solid #0000ff;
  color: #FFFFFF;
}

.question_odd {
  margin-top: 0;
  background-color: #007700;
  border: 1px solid #0000ff;
  color: #FFFFFF;
}

.checked {
  background-color: #333333;
}

</style>

  </head>
  <body>
    <form id="new_timer_form" method="post">
    <label for="name">Set timer: </label>
    M<input type="text" name="new_m" id="new_m" required style="width: 100px;" />
    S<input type="text" name="new_s" id="new_s" required style="width: 100px;" />
     <button type="button" id="hToS" onclick="timeToSpan();">&lt;&lt;&lt; from end time</button>
     H<input type="text" name="hour" id="hour" style="width: 100px;" />&nbsp;:&nbsp;M<input type="text" name="min" id="min" style="width: 100px;" />

    </form>
    <br /><br />
    <button type="button" id="set_timer_button" onclick="setTimer();">Stop and set timer</button>
    <br /><br />
    <button type="button" id="start_timer_button" onclick="startTimer();">Start timer</button>
    <br /><br />

    <p>Current timer:</p>
    <div id="timer">00:00</div>

    <script>


    function timeToSpan() {
    	var form = document.getElementById("new_timer_form");
    	var hF = document.getElementById("hour");
    	var mF = document.getElementById("min");
    	var h = parseInt(hF.value);
    	var m = parseInt(mF.value);
    	if (isNaN(h)) h = 0;
    	if (isNaN(m)) m = 0;
    	if (h < 0) h = 0;
    	if (h > 23) h = 23;
    	if (m < 0) m = 0;
    	if (m > 59) m = 59;
    	hF.value = h;
    	mF.value = m;

    	var t = new Date();
    	var now = t.getTime();
    	t.setHours(h, m, 0);

    	var c = Math.floor((t.getTime() - now) / 1000);


		if (c < 0) return;
    	var nM = Math.floor(c / 60);
    	var nS = Math.floor(c - nM * 60);
    	document.getElementById('new_m').value = nM;
    	document.getElementById('new_s').value = nS;
	}


    function startTimer() {
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
    		  xhttp.open("GET", "timer_api.php?action=start", true);
    		  xhttp.send();
    }


    function setTimer() {
    	// if (confirm('Do you really want to STOP and RESET the timer?')) {
        	var form = document.getElementById("new_timer_form");
        	var formData = new FormData(form);
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
    		  xhttp.open("POST", "timer_api.php?action=reset", true);
    		  xhttp.send(formData);
    		  form.reset();
    	// }
    }


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

    	if (document.activeElement.id !== 'new_m' && document.activeElement.id !== 'new_s')
        {
        	if (document.getElementById('new_m').value === '') document.getElementById('new_m').value = returnData(m);
        	if (document.getElementById('new_s').value === '') document.getElementById('new_s').value = returnData(s);
        }

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
			document.getElementById('timer').style.color = '#000000';
    	}

    }

    function timer() {
		//var span = Math.floor(Date.now() / 1000) - startTime - timer_time + 1;
		var span = Math.floor(Date.now() / 1000) - machineTimeOffset - startTime - timer_time + 1;
		var isNegative = false;

		if (span < 0) {
			isNegative = true;
			span = Math.abs(span);
		}


		var m = Math.floor(span / 60);
		var s = span % 60;

		document.getElementById('timer').innerText = (isNegative?'':'-') + returnData(m) + ':' + returnData(s);
    	document.getElementById('timer').style.color = (isNegative?'#000000':'#FF0000');
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
