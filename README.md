# CSRF Class
Formlarınızda güvenlik için kullanabileceğiniz basit bir csrf sınıfı.

## Kullanımı
> **Not:** Bu sınıf tokenleri depolamak için session kullanır. Her sayfa başında `session_start();` yazmalısınız.
```
<?php
	session_start();

	include("csrf.class.php");

	$class = new CSRF(SÜRESİ_DOLANLAR_SİLİNSİN_Mİ);
	$token = $class->generate(SÜRE, UZUNLUK);

	print_r($token);
?>
```


#### Token oluşturma
```
<?php
	session_start();

	include("csrf.class.php");

	$class = new CSRF(true);
	//$token = $class->generate(SÜRE, UZUNLUK);
	$token = $class->generate(3600, 10);

	print_r($token);
?>

```

#### Süresi dolan tokenleri otomatik sildirmek
Sınıfı başlatırken ilk parametre olarak **true** verirseniz, sınıf her başladığında süresi dolan tokenler otomatik olarak silinecektir.
```
<?php
	session_start();
	include("csrf.class.php");

	$class = new CSRF(true);
?>
```

#### Token onaylama
```
<?php
	if($_POST){
		if($class->check_valid($_POST)){
			echo "GÜvenlik kontrolü başarılı.";
		}else{
			echo "GÜvenlik kontrolü başarısız.";
		}
	}
?>

```

#### Örnek
```
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

```
