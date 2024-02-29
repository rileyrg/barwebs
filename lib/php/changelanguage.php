<?PHP
require_once("utils.php");
setCurrentLanguage($_POST['language']);
header("Location:".getRememberedPage());
?> 