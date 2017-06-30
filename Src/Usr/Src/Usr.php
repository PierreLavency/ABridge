<?php

require_once 'User.php';
require_once 'Session.php';
require_once 'Role.php';
require_once 'Distribution.php';


class Usr
{

	protected static $cleanUp = false;
	protected static $isNew = false;
	protected static $timer= 0; // 0 when connected
	protected $Keep = false;
	
	protected $cookies=[];
	protected $sessions=[];
	protected $changed = false;
	protected $cookieName;
	protected $session;
	
	public static function init($name, $prm)
	{
		$className=$prm[0];
		$id=0;
		if (self::$cleanUp) {
			if (isset($_COOKIE[$name])) {
				unset($_COOKIE[$name]);
			}
		}
		if (isset($_COOKIE[$name])) {
			$id=$_COOKIE[$name];
		}
		$sessionHdl = $className::getSession($id);
		self::$isNew=false;
		if ($sessionHdl->isNew()) {
			self::$isNew=true;
			$id = $sessionHdl->getKey();
			$end = 0;
			if (self::$timer) {
				$end = time() + self::$timer;
			}
			if (php_sapi_name()==='cli') {
				$_COOKIE[$name]=$id;
			} else {
				setcookie($name, $id, $end, "/");
			}
		}
		return $sessionHdl;
	}
	
	public static function isNew()
	{
		return self::$isNew;
	}
}
