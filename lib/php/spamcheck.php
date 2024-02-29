<div class="absoluteanchor">
<div class="spamcheckcontainer">
<div class="spamcheckicon" ><img  src="common-images/icons/antispam.png"/></div>
<?php
$rand_int1 = substr(mt_rand(),0,2);
$rand_int2 = substr(mt_rand(),0,1);
$rand_int3 = substr(mt_rand(),0,1);
$captcha_answer = $rand_int1 + $rand_int2 - $rand_int3;
$_SESSION['captcha_answer'] = $captcha_answer;
echo '<div class="spamcheckq">What is '.$rand_int1.' + '.$rand_int2.' - '.$rand_int3.'?</div><br><input type="text" name="captcha">';
?>
</div>
</div>

