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
if(!isset($_SESSION['pass'])) die();

//password sent should be aded eevery second char staring from 0, 5x base64_enc and then url_enc
//ex
//testpassword
//5tve5shtkpladstsjwkowrvd
//NXR2ZTVzaHRrcGxhZHN0c2p3a293cnZk
//TlhSMlpUVnphSFJyY0d4aFpITjBjMnAzYTI5M2NuWms=
//VGxoU01scFVWbnBoU0ZKeVkwZDRhRnBJVGpCak1uQXpZVEk1TTJOdVdtcz0=
//Vkd4b1UwMXNjRlZXYm5Cb1UwWktlVmt3WkRSaFJuQkpWR3BDYWsxdVFYcFpWRWsxVFRKT2RWZHRjejA9
//VmtkNGIxVXdNWE5qUmxaWFltNUNiMVV3V2t0bFZtdDNXa1JTYUZKdVFrcFdSM0JEWVdzeGRWRlljRnBXUldzeFZGUktUMlJXWkhSamVqQTk=
//VmtkNGIxVXdNWE5qUmxaWFltNUNiMVV3V2t0bFZtdDNXa1JTYUZKdVFrcFdSM0JEWVdzeGRWRlljRnBXUldzeFZGUktUMlJXWkhSamVqQTk%3D

$pass = $_SESSION['pass'];
//unset($_SESSION['pass']); DO not unset - for safari
$pass=urldecode($pass);
$pass=base64_decode($pass);
$pass=base64_decode($pass);
$pass=base64_decode($pass);
$pass=base64_decode($pass);
$pass=base64_decode($pass);
$pass_arr = str_split($pass);
foreach($pass_arr as $key => $value) if(!($key&1)) unset($pass_arr[$key]);
$pass='';
foreach($pass_arr as $key => $value) $pass=$pass.$value;
die($pass);
?>