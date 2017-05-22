<?php
	session_start();
	include("csrf.class.php");

	$class = new CSRF(true);
	$set = $class->generate(3600);

	if($_POST){
		if($class->check_valid($_POST)){
			echo "Doğru.";
		}else{
			echo "Yanlış.";
		}
	}
?>
<form action="" method="post">
	<input type="hidden" name="<?=$set["key"]?>" value="<?=$set["token"]?>">
	<input type="submit" value="Gönder">
</form>