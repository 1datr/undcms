<?php 
// Datascheme object type
define("DSOT_DB",1);
define("DSOT_VIEW",2);

// Datascheme export/import mode
define("DSIE_PHPSERIALIZE",1);
define("DSIE_XML",2);
define("DSIE_JSON",3);

require_once dirName(__FILE__).'/querymanager.php';

class DBScheme extends QMan
{
	
	VAR $_SCHEME = Array();
	VAR $_DRV = null;
	VAR $_EXTBUF = Array();
	VAR $_WORK_PARAMS;
	VAR $_CHANGED = false;
	
	function reset_changed()	// �������� ���� ���������
	{
		$this->_CHANGED=false;
	}
	
	function setdriver(&$drv)
	{
		$this->_DRV = $drv;
		$this->_DRV->_WORK_PARAMS = &$this->_WORK_PARAMS;
		
	}
	
	function  __construct($dbscheme=NULL)
	{
		
		if($dbscheme==NULL)
			$this->_SCHEME = Array();
		elseif(is_string($dbscheme))
			$this->import($dbscheme);
		elseif(is_array($dbscheme))
			$this->_SCHEME = $dbscheme;
		else
			throw new Exception("Wrong datascheme");
	//	echo ">>".dirName(__FILE__).'/config.php'.">>";
		include dirName(__FILE__).'/config.php';
		$this->_WORK_PARAMS=$_DEF_WORK_PARAMS;
		//var_dump($this->_WORK_PARAMS);
		$this->load_extentions();
		
	}
	// object exists
	function obj_exist($objname)
	{
		return !empty($this->_SCHEME[$objname]);
	}
	
	function changed()
	{
		return $this->_CHANGED;
	}
	// add data query
	function x_add($objname,$obj_params=NULL,$objtype=DSOT_DB)
	{
		if(!$this->obj_exist($objname))
			$this->add($objname,$obj_params,$objtype);
		//$this->_CHANGED=true;
	}
	function add($objname,$obj_params=NULL,$objtype=DSOT_DB)
	{
		switch ($objtype)
		{
			case DSOT_DB:
					// event before add table 
					$this->exe_event('before_add_table',
							Array(
								'table'=>$objname,
								'scheme'=>&$this,
								'fields'=>&$obj_params,
								));
					if(xarray_key_exists('#addata', $obj_params))
					{
						$_ADDATA=$obj_params['#addata'];
						unset($obj_params['#addata']);
					}
					if(xarray_key_exists('#defdata', $obj_params))
					{
						if(count($obj_params['#defdata'])>0)
						{
							$_keys = array_keys($obj_params['#defdata']);
					//		var_dump($_keys);
							if(!is_array($obj_params['#defdata'][ $_keys[0] ]))
							{
								$defdata = Array($obj_params['#defdata']);
							//var_dump($defdata);
							}
							else
							{
								$defdata = $obj_params['#defdata'];
							}
							unset($obj_params['#defdata']);
							$this->_SCHEME[$objname] = new DBSTable($objname,$obj_params,$defdata);
						}
					}
					else 
						$this->_SCHEME[$objname] = new DBSTable($objname,$obj_params);
					// event after add table
					$this->exe_event('after_add_table',
							Array(
									'table'=>$objname,
									'fields'=>&$obj_params,
							));
					if(!empty($_ADDATA))
					{
						$this->_SCHEME[$objname]->_ADDATA=$_ADDATA;
					}
					$this->_CHANGED=true;
				break;
			case DSOT_VIEW:
				
				break;
		}
		
	}
	// Export datascheme to file
	function export($fname, $mode=DSIE_PHPSERIALIZE)
	{
		$this->normalize();
		switch ($mode)
		{
			case DSIE_PHPSERIALIZE: 
					$context = serialize($this->_SCHEME);
					file_put_contents($fname, $context);
				break;
			case DSIE_XML:
					require_once $this->_WORK_PARAMS['DIR_INC']."/Serializer.php";
					$serman = new XMLSerializer();
					file_put_contents($fname, $serman->SerializeClass($this->_SCHEME));
				break;
			case DSIE_JSON:
					file_put_contents($fname, json_encode($this->_SCHEME));
				break;
			
		}		
	}
	// scan database structure and make datascheme
	function scandb($scan_data=false)
	{
		$res = $this->_DRV->TableList();
	//	var_dump($res);
		foreach($res as $tbl)
		{
			$tableinfo = $this->_DRV->getTableStruct($tbl);
			$fldlist = Array();
			foreach ($tableinfo as $fld)
			{
				if($fld[0]=='Id')
					continue;
				
				//var_dump($fld);
				
				$fldlist[$fld['Field']]=Array(
						'Type'=>$fld['Type'],
						'Default'=>$fld['Default'],
						"Null"=>$fld["Null"],
						
				);

			}
			if($scan_data)
			{
				$fldlist['#defdata']=$this->_DRV->GetTableRows($tbl);
			}
			else 
			{
				if(xarray_key_exists($tbl, $scan_data))
				{
					$fldlist['#defdata']=$this->_DRV->GetTableRows($tbl);
				}
			}
			$this->add($tbl,$fldlist);
				
			
		}
		//var_dump($this);
	}
	// Import datascheme from file
	function import($fname)
	{
		$content = file_get_contents($fname);
		$explodes=explode('.', $fname);
		$extension = end($explodes);
		switch($extension)
		{
			case 'jsd':
			case 'js':
			case 'jso':
					$this->_SCHEME = json_decode($content);
				break;
			case 'xml':
					require_once $this->_WORK_PARAMS['DIR_INC']."/Serializer.php";
					$serman = new XMLSerializer();
					$this->_SCHEME = xmlrpc_decode($content);
				break;
			case 'ser':
					$this->_SCHEME = unserialize($content);
				break;
		}
	}
	
	// Привести информацию о столбцах таблицы в нормальную форму
	function normalize()
	{
		$_count=0;
		$sch_new=Array();
		$bnd_counts=array();
		foreach ($this->_SCHEME as $tbl => $t)
		{
			if(method_exists($this->_SCHEME[$tbl], 'normalize'))
				$this->_SCHEME[$tbl]->normalize();
			$bnds = $this->_SCHEME[$tbl]->get_bindings();
			$bnd_counts[$tbl]=count($bnds);
		}
		$bnd_counts=sort_hash($bnd_counts);
		foreach($bnd_counts as $k => $v)
		{
			$sch_new[$k]=$this->_SCHEME[$k];
		}
		$this->_SCHEME=$sch_new;
	}
	// �������� ��������� � �� �����
	function normalize_bindings()
	{
		$_BINDS=Array();
		foreach($this->_SCHEME as $key => &$obj)
		{
			foreach ($obj->_FIELDS as $fldkey => &$fld )
			{
			//	
				if( xarray_key_exists($fld, 'bind')) // ���� ������
				{
					
					if($fld['bind']['field_to']=='id') // id
					{
						$fld['Type']='bigint';
					}
					else 
					{
						$fld['Type']=$this->_SCHEME[$fld['bind']['table_to']]->_FIELDS[$fld['bind']['field_to']]['Type'];
					}
				
				}
			}
		}
	}
	// commit all changes in scheme
	function dbcommit()
	{
	//	var_dump($this->_SCHEME);
		$this->normalize();
	//var_dump($this->_SCHEME);
		$this->normalize_bindings();
	//	echo "AFTER::";
	//var_dump($this->_SCHEME);
		// ������ ������ ������� ��, ������� ��� � �����
		$tables = $this->_DRV->TableList();
		
		foreach($tables as $tbl)
		{
		//	echo "<br />>>>$tbl";
			if(empty($this->_SCHEME[$tbl]))
			{
				//var_dump($tbl);
				$this->_DRV->DeleteTable($tbl);
			}
		}
		//echo "::DBCOMMIT::";
		// ���������/��������
		$_DEFDATA=Array();
		foreach($this->_SCHEME as $key => $obj)
		{
			//	echo "\n<br />".count($this->_SCHEME);		
			if(property_exists($obj,"_DEFDATA"))
			{
				if($obj->_DEFDATA!=NULL)
					$_DEFDATA[]=Array('key'=>$key,'defdata'=>$obj->_DEFDATA);
			}
			$this->_DRV->CommitObject($key,$obj);
			
		}
		//echo "::DBCOMMIT::";
		$this->_DRV->CommitBindings();
		//echo "xXX::DBCOMMIT::";
		// write default data
		$this->exe_event('prepare_def_data',Array('defdata'=>&$_DEFDATA,'scheme'=>&$this));
		$this->_DRV->WriteDefData($_DEFDATA,$this);
		
	}
	// get the table
	function gettable($tbl)
	{ 
		global $res;
		$res = &$this->_SCHEME[$tbl];
		return $res;
	}
}
?>