<?php
/*
 * 
 * Form güvenliği için CSRF sınıfı
 *
 * PHP Version 7.x
 *
 * Author: Volkan Yıldız
 *
 * E-mail: siryildiz@gmail.com
 *
 * Version: 1.0
 *
 */
class CSRF {
	private $id_length = 10;

	/**
	 * Sınıf başlatıldığında çalışır
	 * @param boolean $deleteExpiredTokens - Sınıf başlarken, süresi dolan csrf tokenlerinin silinip silinmeyeceğini belirleme
	 * @return void
	 */
	public function __construct($deleteExpiredTokens = false){
		if (!isset($_SESSION["security"]["csrf"])) {
			$_SESSION["security"]["csrf"] = [];
		}
		if($deleteExpiredTokens){
			foreach ($_SESSION["security"]["csrf"] as $key => $value) {
				if(isset($_SESSION["security"]["csrf"])){
					if(@$_SESSION["security"]["csrf"][$key]){
						if(time() >= $_SESSION["security"]["csrf"][$key]["time"] && $_SESSION["security"]["csrf"][$key]["token"]){
							$this->delete($key);
						}
					}
				}
			}
		}
	}

	/**
	 * Yeni bir token ve token keyi oluşturma fonksiyonu
	 * @param int $time - Tokenin hayatta kalacağı süre (saniye cinsinden)
	 * @param int $length - Oluşturulacak tokenin karakter uzunluğu
	 * @return array
	*/
	public function generate($time = 3600, $length = 10){
		if(isset($_SESSION["security"]["csrf"])){
			$token_id = $this->random($length);
			$token = $this->set_token();
			$_SESSION["security"]["csrf"][$token_id] = array("token" => $token, "time" => (time()+$time));
			return array("key" => $token_id, "token" => $token);
		}
	}

	/**
	 * Yeni bir token oluşturma
	 * @return string
	*/
	public function set_token(){
		if(isset($_SESSION["security"]["csrf"])){
			$token = base64_encode(hash('sha256', $this->random(500)));
			return $token;
		}
	}

	/**
	 * Dizi içinden token kontrol etme
	 * @param array $array - $_POST veya $_GET kullanılabilir
	 * @return boolean
	*/
	public function check_valid($array) {
		foreach ($array as $key => $value) {
			if(isset($_SESSION["security"]["csrf"])){
				if(@$_SESSION["security"]["csrf"][$key]){
					if(time() <= $_SESSION["security"]["csrf"][$key]["time"] && $_SESSION["security"]["csrf"][$key]["token"] == $value){
						return true;
					}
					$this->delete($key);
					return false;
				}
				return false;
			}
			return false;
		}
		return false;
	}

	/**
	 * Token silme
	 * @param string $key
	 * @return void
	*/
	public function delete($key){
		if(isset($_SESSION["security"]["csrf"])){
			if(@$_SESSION["security"]["csrf"][$key]){
				unset($_SESSION["security"]["csrf"][$key]);
			}
		}
	}


	/**
	 * Rastgele string üretimi
	 * @param int $len - Karakter uzunluğu
	 * @return string
	*/
	private function random($len) {
        if (function_exists('openssl_random_pseudo_bytes')) {
                $byteLen = intval(($len / 2) + 1);
                $return = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
        } elseif (@is_readable('/dev/urandom')) {
                $f=fopen('/dev/urandom', 'r');
                $urandom=fread($f, $len);
                fclose($f);
                $return = '';
        }
 
        if (empty($return)) {
                for ($i=0;$i<$len;++$i) {
                        if (!isset($urandom)) {
                                if ($i%2==0) {
                                             mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000);
                                }
                                $rand=48+mt_rand()%64;
                        } else {
                                $rand=48+ord($urandom[$i])%64;
                        }
 
                        if ($rand>57)
                                $rand+=7;
                        if ($rand>90)
                                $rand+=6;
 
                        if ($rand==123) $rand=52;
                        if ($rand==124) $rand=53;
                        $return.=chr($rand);
                }
        }
 
        return $return;
	}

}

?>