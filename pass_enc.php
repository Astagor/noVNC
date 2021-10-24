<pre><?php 
if(!isset($_GET['pass'])) die();
$pass = $_GET['pass'];

$seed = str_split('abcdefghijklmnopqrstuvwxyz'
    .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    .'0123456789!@#$%^&*()'); // and any other characters
shuffle($seed); // probably optional since array_is randomized; this may be redundant
$pass_arr = str_split($pass);


echo($pass);
echo("\n");

$pass = '';
foreach($pass_arr as $key => $value) {
    $pass = $pass.$seed[array_rand($seed)].$value;
}
echo($pass);
echo("\n");

$pass=base64_encode($pass);
echo($pass);
echo("\n");

$pass=base64_encode($pass);
echo($pass);
echo("\n");

$pass=base64_encode($pass);
echo($pass);
echo("\n");

$pass=base64_encode($pass);
echo($pass);
echo("\n");

$pass=base64_encode($pass);
echo($pass);
echo("\n");

$pass=urlencode($pass);
echo($pass);
echo("\n");
echo("\n");
$pass=urldecode($pass);
echo($pass);
echo("\n");

$pass=base64_decode($pass);
echo($pass);
echo("\n");

$pass=base64_decode($pass);
echo($pass);
echo("\n");

$pass=base64_decode($pass);
echo($pass);
echo("\n");

$pass=base64_decode($pass);
echo($pass);
echo("\n");

$pass=base64_decode($pass);
echo($pass);
echo("\n");

$pass_arr = str_split($pass);
foreach($pass_arr as $key => $value) if(!($key&1)) unset($pass_arr[$key]);
$pass='';
foreach($pass_arr as $key => $value) $pass=$pass.$value;
die($pass);
?>
</pre>