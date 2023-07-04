<?php
if(!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION['medVC_auth'])) {
    if (isset($_POST['medvc_admin_login']) && isset($_POST['medvc_admin_password']) && $_POST['medvc_admin_login']==='admin' && $_POST['medvc_admin_password'] === 'ErykRulez') {
        $_SESSION['medVC_auth'] = true;
        header('Location: '.$_SERVER['REQUEST_URI']);
        die();
    } else {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>medVC Login</title>
	<meta charset="utf-8" />
</head>
<body>
	<form action="<?php echo($_SERVER['REQUEST_URI']);?>" method="post">
		<label for="medvc_admin_login"><b>Login</b></label>
		<input type="text" placeholder="Enter Login" name="medvc_admin_login" required>
		<label for="medvc_admin_password"><b>Password</b></label>
		<input type="password" placeholder="Enter Password" name="medvc_admin_password" required>
		<button type="submit">Login</button>
	</form>
</body>
</html>
    <?php
        die();
    }
}
?>