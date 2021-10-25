<?php 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type: application/json');

if(!isset($_REQUEST['action'])) die();

$emptyJson = array('m' => 0, 's' => 0, 'started' => false, 'startTime' => time());
$fileName = 'timer.txt';
$action = $_REQUEST['action'];
$currentTime = time();


if (file_exists($fileName)) {
    $q = json_decode(file_get_contents($fileName), true);
} else {
    $q = json_decode(json_encode($emptyJson), true); //trick to copy array
    file_put_contents($fileName, json_encode($q), LOCK_EX);
}

if ($action === 'get') {
    
    $q['currentTime'] = $currentTime;
    die(json_encode($q));
    
} elseif ($action === 'reset'){
    if (isset($_REQUEST['new_m']) && isset($_REQUEST['new_s'])){
        
        $newM = intval(trim($_REQUEST['new_m']));
        $newS = intval(trim($_REQUEST['new_s']));
        
        $q = json_decode(file_get_contents($fileName), true);
        
        $q['m'] = $newM;
        $q['s'] = $newS;
        $q['startTime'] = 0;
        $q['started'] = false;

        file_put_contents($fileName, json_encode($q), LOCK_EX);

    }
    
    $q['currentTime'] = $currentTime;
    die(json_encode($q));
    
} elseif ($action === 'start') {
    
    if (!$q['started']) {
        $q['started'] = true;
        $q['startTime'] = $currentTime; //in sec
    
        file_put_contents($fileName, json_encode($q), LOCK_EX);
    }
    
    $q['currentTime'] = time();
    die(json_encode($q));
    
} elseif ($action === 'totime'){
    
    if (isset($_REQUEST['h']) && isset($_REQUEST['m'])){
        
        $h = intval(trim($_REQUEST['h']));
        $m = intval(trim($_REQUEST['m']));
        
        $t = new DateTime();
        $now = $t->getTimestamp();
        
        $t->setTime($h, $m);
        
        $c = $t->getTimestamp() - $now;
        
        
        if ($c >= 0) {
          
            $newM = floor($c / 60);
            $newS = floor($c - $newM * 60);
            
            $q = json_decode(file_get_contents($fileName), true);
            
            $q['m'] = $newM;
            $q['s'] = $newS;
            $q['startTime'] = $currentTime;
            $q['started'] = true;
            
            file_put_contents($fileName, json_encode($q), LOCK_EX);
        }
        
    }
    
    $q['currentTime'] = $currentTime;
    die(json_encode($q));
}
die();
?>