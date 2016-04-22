<?php 
class db { 
	var $conn_params;
	var $scheme;
	var $drv;
	
	function  __construct($dbparams,$drvtype="Mysql",$dbscheme=NULL)
	{
		try {
			
			$drvclass = "DBD_".$drvtype;
			if(!class_exists($drvclass))
				throw new Exception("Driver $drvtype is not exist or not enabled");
			$this->drv = new $drvclass();
			// scheme
			$this->scheme = new DBScheme($dbscheme);
			
			$this->drv->_WORK_PARAMS = &$this->scheme->_WORK_PARAMS;
			$this->drv->Connect($dbparams);			
				 
			$this->scheme->setdriver($this->drv);
		}
		catch(Exception $e)
		{
			echo '<br />��������� ����������: ',  $e->getMessage(), "\n";
		}
	}
	
	// commit database scheme
	function commit()
	{
		$this->scheme->dbcommit();
	}
}
?>