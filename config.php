<?php 
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
$_QSKIP=false;
$_LIBS =Array('xutils','jqutils','bootstrap','simplehtmldom');
$_COMPONENTS = Array('dbman');
$_PSEUDOFOLDER=true;
// database connections
$_PROFILES=Array(
	'default'=>Array(
		'connection'=>Array(
				'host'=>'localhost',
				'user'=>'root',
				'password'=>'',//'123456',
				'dbname'=>'ideaman',
				'prefix'=>'ucs_',
				'charset'=>'utf8',
				'subcharset'=>'utf8_general_ci'
			),
		'langs'=>Array('en'=>'English','ru'=>'�������'),
		'currlang'=>'en',
		),		
);


//$_LANGS=Array('en'=>'English','ru'=>'�������');
?>