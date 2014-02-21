<?php

use SugiPHP\Captcha\Captcha;

include __DIR__."/../vendor/autoload.php";

session_start();
if (isset($_POST["captcha"])) {
	if (empty($_SESSION["captcha"])) {
		$msg = "Enter code";
	} elseif ($_SESSION["captcha"] !== $_POST["captcha"]) {
		$msg = "Wrong code";
	} else {
		unset($_SESSION["captcha"]);
		die("<h1>Code accepted</h1><a href=''>Try again</a>");
	}
} else {
	$msg = "";
}


$config = array("charset" => "абвгдежзийклмнопрстуфхцчшщъьюя1234567890");
$config = array();
$captcha = new Captcha($config);
$captcha->build();
$_SESSION["captcha"] = $captcha->getCode();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
</head>
<body>
	<h1><?=$msg;?></h1>
	<img src="<?=$captcha->base64();?>" />
	<form method="post" action="">
		<input type="text" name="captcha" value="" />
		<input type="submit" value="Submit" />
	</form>
</body>
</html>
