<?php
require_once('recaptchalib.php');
$privatekey = "6LcERggAAAAAAIwtXP4aWPxlwLd_KjEJBNIqjw4x";
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
	die ('<div id="captchaerror">'.sysMsg("admin.captacha.error","Error verifying confirmation code. Try Again.").'</div>');
}
?>
