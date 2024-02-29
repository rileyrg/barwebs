<?php
global $localLoadLibs;
$localLoadLibs[]='barwebs.languageselect.js';
?>
<select class="language"  id="language" name="language">
<?php echo addLanguageSelectValues(getCurrentLanguage());?>
</select>

