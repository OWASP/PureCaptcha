<?php
session_start();
function checkCaptcha($response)
{
    if (isset($_SESSION['captcha_login_form']) && strtolower($_SESSION['captcha_login_form']) === strtolower($response))
        $res = true;
    else
        $res = false;
    //this has to be done everytime you check captcha
    //otherwise your captcha is ineffective (not one-time)
    unset($_SESSION['captcha_login_form']); 
    return $res;
}
if (isset($_POST['CAPTCHA']))
    if (checkCaptcha($_POST['CAPTCHA']))
        echo "Valid.";
    else
        echo "Invalid.";
?>


<form method='post'>
<label>Captcha:</label>
<img id='captcha' src='purecaptcha_img.php?t=login_form' height='22'/><a onclick='var t=document.getElementById("captcha"); t.src=t.src+"&amp;"+Math.random();' style="padding-left:20px; text-decoration:none; cursor:pointer ; color:red">(Reload)</a>
<br/>
<input type='text' name='CAPTCHA' placeholder='captcha'  size="20"/><br/>

<input type='submit' />
</form>