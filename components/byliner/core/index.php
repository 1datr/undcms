<?php 

require_once dirName(__FILE__).'/config.php';


class byliner {
	
	var $_onstep;
	var $_STEP;
	var $_VARS;
	
	function __construct()
	{
		session_start();
		if(empty($_SESSION['_STEP']))
			$_SESSION['_STEP']=0;
		$this->_STEP = $_SESSION['_STEP'];
		
		//var_dump($_SESSION);
	}
		
	function SetStep($i)
	{
		$_SESSION['_STEP']=$i;
	}
	
	function GetStep()
	{
		return $_SESSION['_STEP'];
	}
		
	function SetVar($vname,$vval)
	{
		//var_dump($vname);
		
		$_SESSION['_VARS'][$vname]=$vval;
		
	}
	
	function GetVar($vname)
	{
		return $_SESSION['_VARS'][$vname];
	}
	
	
	function InitVar($vname,$defval)
	{
		if(empty( $_SESSION['_VARS'][$vname]))
			$_SESSION['_VARS'][$vname]=$defval;
	}
	
	function terminate()
	{
		
	}
	
	function exe_step()
	{
		if (isset($this->_onstep)){
			
			call_user_func($this->_onstep, Array('bl'=>$this));
	
		}
		else{
			echo 'no step handler'; //это вывод для тестирования
		}
	}
}
?>