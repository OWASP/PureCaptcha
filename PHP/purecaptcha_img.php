<?php
session_start();
if (!isset($_GET['t']))
    $title = "default";
else
    $title = $_GET['t'];


require_once "purecaptcha.php";

$p = new PureCaptcha();
$_SESSION["captcha_{$title}"] = $p->show();