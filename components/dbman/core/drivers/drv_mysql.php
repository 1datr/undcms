<?php 
/*
 * 			MySQL database driver
 * 
 * */
class DBD_Mysql extends DBDriver
{
	var $_LINK;
	var $_PREFIX;
	
		
	// Select database
	function SelectDB($dbname)
	{
		if(mysql_select_db($dbname,$this->_LINK))
		{
			$this->currentdb = $dbname;
			return true;
		}
		return false;
	}
	
	var $connData;
	
	function ConnectDBServer($connData)
	{
		$this->connData = $connData;
		if(!is_array($connData)) $connData=Array();
		if(empty($connData['server'])) $connData['server']='localhost';
		if(empty($connData['user'])) $connData['user']='root';
		if(empty($connData['password'])) $connData['password']='';
		$this->_LINK= mysql_pconnect($connData['server'], $connData['user'], $connData['password'])
		or die("Could not connect: " . mysql_error());
		$this->exe_query("SET NAMES '".$connData['charset']."';");
		$this->exe_query("SET CHARACTER SET '".$connData['charset']."';");
		$this->exe_query("SET SESSION collation_connection = '".$connData['subcharset']."';");
	}
	
	// Disconnect from db
	function Disonnect($disconnectvar=NULL)
	{
		
		mysql_disconnect($this->_LINK);
	}
	
	VAR $_DBCREATED=FALSE;
	
	function CreateDB($dbname)
	{
		global $_DEF_CHARSET;
		global $_DEF_SUBCHARSET;
		$qdbcreate = "CREATE DATABASE  $dbname CHARACTER SET ".$this->connData['charset']." COLLATE ".$this->connData['subcharset'];
		
		$this->exe_query($qdbcreate);
		$this->_DBCREATED=true;
	}
	
	function CheckField($fld)
	{
		return  $fld;
	}
	
	function GetFieldStr($fldinfo,$name)
	{
		$fldstr = "";
		$charsetstr = "";
		if($fldinfo['charset']!="")
		{
			$charsetstr = "CHARACTER SET {$fldinfo['charset']} COLLATE {$fldinfo['sub_charset']}";
			$sqlout=true;
		}
		$nullstr = ($fldinfo['Null']=="NO") ? "Not null" : "Null";
		if($fldinfo['Default']==NULL)
			$fldstr = "`$name` ".$fldinfo['Type']." $charsetstr ".$nullstr;
		else
			$fldstr = "`$name` ".$fldinfo['Type']." $charsetstr Default ".$fldinfo['Default']." ".$nullstr;
		return $fldstr;
	}
	// Create table
	function CreateTable($tblname,$TableData)
	{ 
	//	var_dump($TableData->_FIELDS);
		//CREATE TABLE
		$sql = "CREATE TABLE `".$this->_PREFIX.$tblname."` (
		`id` BIGINT(20) NOT NULL AUTO_INCREMENT,\n";
		$sqlout = false;
		$fld_code_buf = Array();
		foreach ($TableData->_FIELDS as $name => $fld)
		{
			
			if(!$fld['virtual'])
			{	
				$fld_code_buf[]=$this->GetFieldStr($fld,$name);					
			}
		}
		$sql = $sql.implode(",",$fld_code_buf)." \n";
		
		$sql = $sql.",
	PRIMARY KEY(`id`)
	)";
		global $_QDEBUG;
		if($_QDEBUG)
			echo "[".__LINE__."]".$sql."<br/>";
		$this->dbg(__LINE__,$sql);
		
		$this->exe_query($sql);
			
		foreach ($TableData->_FIELDS as $name => $fld)
		{
			if(!empty($fld['bind']))
				$this->_BINDINGS[]=Array(
						'tblname'=>$tblname,
						'field'=>$name,
						'bind_data'=>$fld['bind'],
				);
				//$this->create_binding($tblname, $name, $fldinfo['bind']);
		}
			
			
		/*	
		 
		 ALTER TABLE  `ucs_group` CHANGE  `parent`  `parent` BIGINT( 20 ) NULL ;
	
		*/
	}
	
	function dbg($_LINE,$sql)
	{
		global $_QDEBUG;
		if($_QDEBUG)
			echo "[".$_LINE." (".__FILE__.")]".$sql."<br/>";
		
	}
	// Get table stucture
	function getTableStruct($tblname)
	{
		$arr = Array();
		$res = $this->exe_query("SHOW COLUMNS FROM ".$this->_PREFIX.$tblname);
		while($row = mysql_fetch_array($res))
		{
			
			$constraints = $this->GetConstraints($tblname,$row['Field']);
			$row['constraints'] = $constraints;
			
			$arr[]=$row;
		}
		//var_dump($arr);
		return $arr;
	}
	
	function res_count($res)
	{
		return mysql_num_rows($res);
	}	
	
	function WriteDefDataItem(&$nfo,&$scheme=null)
	{
		foreach ($nfo['defdata'] as $idx => $dditem)
		{
			$where = "";
			$i=0;
		
			foreach ($dditem as $fld => $val)
			{
				
				if($val[0]!='@')
				{
					if($i)
						$where = "$where AND ";
					$where = "$where `$fld`='$val'";
					$i++;
				}
			}
			$qselect = "SELECT * FROM {$this->_PREFIX}".$nfo['key']." WHERE $where";
			$this->dbg(__LINE__,$qselect);
			//echo ">>> $qselect";
			$res_select = $this->exe_query($qselect);
			$selcount = $this->res_count($res_select);
			// if exists - delete
			if($selcount>0)
			{
				unset($nfo['defdata'][$idx]);
			}
		}
		
			
		// no such data
		if(count($nfo['defdata']))
		{
			$q = $this->q_add(
					Array(
							'table'=>$nfo['key'],
							'data'=>$nfo['defdata'],
					)
			);
			$this->dbg(__LINE__,$q);
			$this->exe_query($q);
		}
	}
	// write default data to tables
	function WriteDefData($defdata=NULL,&$scheme=null)
	{
		foreach ($defdata as $key => $nfo)
		{
			// make exist queries
			//if($scheme->_SCHEME[$key])
			$this->WriteDefDataItem($nfo,$scheme);	
		}
	}
	
	function DeleteConstraint($tbl,$ckey)
	{
		$query = "ALTER TABLE `".$this->_PREFIX.$tbl."` DROP FOREIGN KEY $ckey ";
		$this->dbg(__LINE__,$query);
		$this->exe_query($query,false);
	}
	// Get rows of the table
	function GetTableRows($tbl)
	{
		$arr = Array();
		$res = $this->exe_query("SELECT * FROM `".$this->_PREFIX.$tbl."`");
		while ($row = $this->res_row($res))
		{
			$arr[]=$row;
		}
		return $arr;
	}
	// delete field of table
	function DeleteField($fld,$tbl)
	{
		$keyname = "key_$tbl_$fld";
		
		$this->DeleteConstraint($tbl,$keyname);
		
		$this->exe_query("ALTER TABLE `".$this->_PREFIX.$tbl."` DROP `$fld`");
	}
	// Change table
	function ChangeTable($tblname,$TableData)
	{ 
		// show all columns of table
		$res = $this->exe_query("SHOW COLUMNS FROM ".$this->_PREFIX.$tblname);
		$fields = Array();
		$fildinfo = Array();
		// walk all columns
		while($row = mysql_fetch_array($res))
		{

			if(empty($TableData->_FIELDS[$row[0]]))	// no this columns in scheme
			{
				if($row[0]!='id')
					$this->DeleteField($row[0],$tblname);
					
			}
			elseif ($TableData->_FIELDS[$row[0]]['virtual'])
			{
				$this->exe_query("ALTER TABLE `".$this->_PREFIX.$tblname."` DROP `".$row[0]."`");
				
			}
			else 
			{
				// Watch the difference between field image and the real field 
				$fldinfo = $TableData->_FIELDS[$row[0]];
				
				if(!$fldinfo['virtual'])
				{
					//var_dump($TableData->_FIELDS[$row[0]]);
					if(!empty($TableData->_FIELDS[$row[0]]['bind']))
					{
						//	echo ">>";
						//var_dump($TableData->_FIELDS);
						
						$this->_BINDINGS[]=Array(
								'tblname'=>$tblname,
								'field'=>$row[0],
								'bind_data'=>&$TableData->_FIELDS[$row[0]]['bind'],
						);
						
						
					}
			/*	
			 * ALTER TABLE  `tdb_user` CHANGE  `name`  `name` TEXT CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL ;
			 * 
			 * */
					$str_collate = "";
					if($fldinfo['charset']!='')
					{
						$str_collate = " CHARACTER SET ".$fldinfo['charset']." COLLATE ".$fldinfo['sub_charset']." ";
					
					}
				
		/*		if(($row['Type']!=$fldinfo['Type'])||($row['Null']!=$fldinfo['Null']))
				{*/
				//	$fldinfo = $this->normalized_field($fld);
					$nullstr = ($fldinfo['Null']=="NO") ? "NOT NULL" : "NULL";
					if($fldinfo['Default']==NULL)
						$fldstr = "`".$row[0]."` ".$fldinfo['Type']." $str_collate  ".$nullstr;
					else
						$fldstr = "`".$row[0]."` ".$fldinfo['Type']." $str_collate  Default ".$fldinfo['Default']." ".$nullstr;
					$_sql = "ALTER TABLE  `".$this->_PREFIX.$tblname."` CHANGE  `".$row[0]."`  $fldstr ";
					$this->dbg(__LINE__,$_sql);
					$this->exe_query($_sql);
			//	}
				
					$fields[]=$row[0];
				}
			}
			
			
		}
		
		foreach($TableData->_FIELDS as $fldname => $fldimage)
		{
		
			if(!in_array($fldname, $fields))
			{
				$fldinfo = $this->CheckField($fldimage);
				if(!$fldinfo['virtual'])
				{
					$nullstr = ($fldinfo['Null']=="NO") ? "NOT NULL" : "NULL";
					if($fldinfo['Default']==NULL)
						$sql = "ALTER TABLE `".$this->_PREFIX.$tblname."` ADD `$fldname` ".$fldinfo['Type']." $nullstr";
					else
						$sql = "ALTER TABLE `".$this->_PREFIX.$tblname."` ADD `$fldname` ".$fldinfo['Type']." Default ".$fldinfo['Default']." $nullstr";
					global $_QDEBUG;
					if($_QDEBUG)
							echo "[".__LINE__."]".$sql."<br/>";				//var_dump($sql);
					$this->exe_query($sql);
				}
			}
			
			
		}
		//var_dump($fields);
	}
	// Delete table
	function DeleteTable($table)
	{ 
	
		$this->exe_query("DROP TABLE `".$this->_PREFIX.$table."` CASCADE");
	}
	
	// Select queries
	function Select($selectdata)
	{
		
	}
	
	function existsTable($table) {
		$res = $this->exe_query("SELECT * FROM `".$this->_PREFIX.$table."` LIMIT 1",FALSE);
		$err_no = mysql_errno();
		return ($err_no != '1146' && $res = true);
	}
	
	function MakeFieldStr($fld_array)
	{
		//return $fld_array['type']
	}
	// Get constraints of table optionally - field constraint name
	function GetConstraints($table,$field=NULL,$conname=NULL)
	{
		$where = "";
		if($field!=NULL)
			$where = " AND COLUMN_NAME = '$field' ";
		if($conname!=NULL)
		{
			if($where!="")
				$where = "$where AND ";
			$where = "$where CONSTRAINT_NAME = $conname";
		}
		$sql = "SELECT * 
 FROM information_schema.KEY_COLUMN_USAGE 
 WHERE TABLE_SCHEMA ='{$this->currentdb}' AND TABLE_NAME ='".$this->_PREFIX.$table."' AND 
 CONSTRAINT_NAME <>'PRIMARY' AND REFERENCED_TABLE_NAME 
 is not null $where";
		$res = $this->exe_query($sql);
		$rows = Array();
		while($row = $this->res_row($res))
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	function create_binding($tblname,$field,$bind_data)
	{
		$conns = $this->GetConstraints($tblname,$field);
		//var_dump($conns);
		foreach($conns as $conn)
		{
			$this->DeleteConstraint($tblname,$conn['CONSTRAINT_NAME']);
		}
		
		$this->exe_query("ALTER TABLE `{$this->_PREFIX}$tblname` ADD INDEX ( `$field` ) ",false);
		
		if(empty($bind_data['on_delete']))
			$bind_data['on_delete']='CASCADE';
		if(empty($bind_data['on_update']))
			$bind_data['on_update']='RESTRICT';
		
		$keyname = "key_".$tblname."_".$field;
		
		$query = "ALTER TABLE `".$this->_PREFIX.$tblname."` 
ADD CONSTRAINT `$keyname` FOREIGN KEY ( `$field` ) 
REFERENCES `".$this->_PREFIX.$bind_data['table_to']."` (`".$bind_data['field_to']."`) 
ON DELETE ".$bind_data['on_delete']." ON UPDATE ".$bind_data['on_update']."";
		
	//	echo "$query<br /><br />";
	
		$this->dbg(__LINE__,$query);
		
		$this->exe_query($query);
		
	}
	// List of tavble in database now
	function TableList($where=1)
	{
		if($where==1)
			$where = "`table` LIKE '{$this->_PREFIX}%'";
		$res = $this->exe_query("SHOW TABLES");
		$arr = Array();
		//echo "SHOW OPEN TABLES WHERE $where";
		while($row = mysql_fetch_array($res))
		{

			$arr[]= substr($row[0],strlen($this->_PREFIX));
		}
		//var_dump($arr);
		return $arr;
	}
	// id's of last added records
	function last_added_ids($table)
	{
		$lastid = mysql_insert_id();
		$q = "SELECT id FROM `{$this->_PREFIX}$table` WHERE id>=$lastid";
		$res = $this->exe_query($q);
		$ids = Array();
		while($row = $this->res_row($res))
		{
			$ids[]=$row['id'];
		}
		return $ids;
	}
	
	function prepare_data(&$data_arr)
	{
		if(is_array($data_arr))
		{
			foreach ($data_arr as $k => &$v)
			{
				if(!is_array($v))
				{
					$v=mysql_real_escape_string($v);
				}
				else 
					$this->prepare_data($v);
			}
		}
		else
		{
			$data_arr=mysql_real_escape_string($data_arr);
		}
	}
	
	function exe_query($q,$exept=true)
	{
		global $_QSKIP;
		global $_QFILEDUMP;
		$q=strtr($q,Array("\n"=>'',"\r"=>''));
		//$q= htmlentities($q,$this->_LINK);
		$res = mysql_query($q,$this->_LINK);
		if($_QFILEDUMP)
		{
			if(!file_exists('.query'))
				$_text = "";
			else
				$_text = file_get_contents('.query');
			$_text = "$_text
			
			$q";
			file_put_contents('.query',$_text);
		}
		if($exept && !$_QSKIP)
		{
			if($res==FALSE)
			{
				echo "Bad query result [$q] (".mysql_errno().": ".mysql_error().")  ";
				throw new Exception("Bad query result [$q] (".mysql_errno().": ".mysql_error().")  ");
			}
		}
		return $res;
	}
	
	function result_row_by_number($res,$rowid,$fld=null)
	{
		if($fld==null)
			return mysql_result($res,$rowid);
		else
			return mysql_result($res,$rowid,$fld);
	}
	// get row of result
	function res_row($res)
	{
		return mysql_fetch_array($res,MYSQL_ASSOC);
	}
	// get associative array of normalized fields
	function make_select_str($selects,$_PREFIX='')
	{
		//echo "::>>";
	//	var_dump($selects);
		
		if(count($selects)==0) return '*';
		if($selects=='*') return '*';
		$str_select="";
		$i=0;
		$_selects = Array();
		foreach ($selects as $selkey => $selitem)
		{
			/*if(!is_string($selkey))
			{
				$str_select = $str_select.", $selitem";
			}
			else
			{*/
				if(is_string($selitem))
				{
					$_selects[] = $selitem;
				}
				else
				{
					if($selitem['fld']=='*')
					{
						$_selects[] =  "$_PREFIX{$selitem['table']}.{$selitem['fld']}";
					}
					else
					{
						$_selects[] = "$_PREFIX{$selitem['table']}.{$selitem['fld']} as `$selkey`";
					
					}
						
				}
			//}
			$i++;
		}
		$_str = implode(',',$_selects);
		$_str = strtr($_str,Array('@@'=>$this->_PREFIX));
		return $_str;
	}
	// join  t2 on t1.f1=t2.f2
	function make_join_str($joins,&$select_params,$_PREFIX='')
	{
		$str_join = "";
		$tbl_last = $select_params['table'];
		foreach ($joins as $jkey => $jval )
		{
			/*	
			if($jval['to']['table']==$jkey)
				$str_join = $str_join." {$jval['jtype']} join
				$_PREFIX$jkey as `$_PREFIX{$jval['to']['table_as']}` on
				$_PREFIX{$jval['from']['table']}.{$jval['from']['field']}=".$_PREFIX.$jkey.'.'.$jval['to']['field']." ";
			else
				$str_join = $str_join." {$jval['jtype']} join
				$_PREFIX{$jval['to']['table']} as `$_PREFIX{$jval['to']['table_as']}` on
				$_PREFIX{$jval['from']['table']}.{$jval['from']['field']}=$_PREFIX.$jkey.{$jval['to']['field']} ";
			*/
			if($jval['to']['table']==$jkey)
				$str_join = $str_join." {$jval['jtype']} join
				$_PREFIX$jkey as `$_PREFIX{$jval['to']['table_as']}` on
				$_PREFIX{$jval['from']['table']}.{$jval['from']['field']}=$_PREFIX{$jval['to']['table_as']}.{$jval['to']['field']} ";
			else
				$str_join = $str_join." {$jval['jtype']} join
				$_PREFIX{$jval['to']['table']} as `$_PREFIX{$jval['to']['table_as']}` on
				$_PREFIX{$jval['from']['table']}.{$jval['from']['field']}=$_PREFIX{$jval['to']['table_as']}.{$jval['to']['field']} ";
		
			
			//$tbl_last = $jval['jtable'];
		}
	
		//$str_from = $_PREFIX.$select_params['table'].$str_join;
		return $str_join;
	}
	
	
	// make where section
	function makewhere($wherestr,$_PREFIX)
	{
	
	// @@ - is prefix
	$_WHERE = strtr($wherestr,Array('@@'=>$_PREFIX));
	/*	$WHEREBUF=$args['WHEREBUF'];
		if(count($WHEREBUF))
		{
		if($_WHERE==1)
			$_WHERE="1 ";
			foreach($WHEREBUF as $idx => $whereitem)
			{
			// op1
			//
			if( is_string($whereitem['op1']))
				$whereitem['op1']="'".$whereitem['op1']."'";
				//
				if(xarray_key_exists('field', $whereitem['op1']))
				{
			if(xarray_key_exists('table', $whereitem['op1']))
			{
			$whereitem['op1']=$_PREFIX.$whereitem['op1']['table'].".".$whereitem['op1']['field'];
			}
					else
			{
					$whereitem['op1']=$_PREFIX.$args['table'].".".$whereitem['op1']['field'];
						
			}
			}
			// op2
					// is string
				if( is_string($whereitem['op2']))
					$whereitem['op2']="'".$whereitem['op2']."'";
							//
					if(xarray_key_exists('field', $whereitem['op2']))
					{
						if(xarray_key_exists('table', $whereitem['op2']))
						{
							$whereitem['op2']=$_PREFIX.$whereitem['op2']['table'].".".$whereitem['op2']['field'];
						}
						else
						{
							$whereitem['op2']=$_PREFIX.$args['table'].".".$whereitem['op2']['field'];
						}
					}
												
			
				$_WHERE = $_WHERE." AND ".$whereitem['op1'].$whereitem['op'].$whereitem['op2'];
												
			}
		}
		*/
	return $_WHERE;
	}
	// query select
	function q_select($select_params)
	{
		// select [t1.f1, t2,f2 ...]

		// add field to selects list
		
		
		$str_select = "*";
		$str_where=1;
		$limit="";
		
		$select_items = Array();
		$joins = Array();
		
		if(!empty($select_params['join']))
		{
			foreach ($select_params['join'] as $jkey => $j)
			{
				$joins[$jkey]=$j;				
			}
			
		}
		
		$_limit = "";
		if(!empty($select_params['limit']))
		{
			$l1 = ($select_params['limit']['page']-1) * $select_params['limit']['size'];
			$_limit = " LIMIT $l1,".$select_params['limit']['size'];
		}
		
	/*	echo "::>>";
		var_dump($select_params);*/
		
		$sql = "SELECT ".$this->make_select_str($select_params['select'],$this->_PREFIX)." FROM ".$this->_PREFIX.$select_params['table']." ".
				$this->make_join_str($select_params['joins'],$select_params,$this->_PREFIX)." ".
				" WHERE ".$this->makewhere($select_params['where'],$this->_PREFIX)." ".
				$this->make_group($select_params['group'])." ".
				$this->make_having($select_params)." ".
				$this->make_order($select_params['order'])." ".
				$_limit ;
		$this->dbg(__LINE__,$sql);
		return $sql;

	}
	
	function make_having($select_params)
	{
		if(count($select_params['group'])==0) return "";
		if($select_params['having']=="") return "";
		$select_params['having'] = strtr($select_params['having'],Array('@@'=>$this->_PREFIX));
		return "HAVING ".$select_params['having'];
	}
	
	function make_order($orders)
	{
		if(count($orders)==0) return "";
		return "ORDER BY ".ximplode(',',$orders,"{fld} {dir}",Array('@@'=>$this->_PREFIX));
	}
	
	function make_group($groups)
	{
		if(count($groups)==0) return "";
		return "GROUP BY ".ximplode(',',$groups,"{0}",Array('@@'=>$this->_PREFIX));
	}
	
		
	// query delete
	function q_delete($del_params)
	{
		if(empty($del_params['where']))
			$del_params['where']=1;
		$sql = "DELETE FROM `{$this->_PREFIX}{$del_params['table']}` WHERE ".$this->makewhere($del_params['where'],$this->_PREFIX);
		$this->dbg(__LINE__,$sql);
		return $sql;
	
	}
	// query select
	function q_delete_item($params)
	{			
		$sql = "DELETE FROM `{$this->_PREFIX}{$params['table']}` WHERE id=".$params['id'];
		$this->dbg(__LINE__,$sql);
		return $sql;
	}
	// query add
	function q_add($add_data)
	{		
	//	var_dump($add_data);
		$sql1 = "INSERT INTO `{$this->_PREFIX}{$add_data['table']}` (";
		$sql2 = ") VALUES ";
		$j = 0;
//		var_dump($add_data);
		foreach($add_data['data'] as $k => $v)
		{
			
			if($j==0)
				$ins = " (NULL";
			else 
				$ins = ", (NULL";
			$i=0;
			if($j==0)
				$fldstr = "`id`";
			foreach($v as $fld => $val)
			{
				if(($fld[0]=='/')||($fld[0]=='\\')) // virtual field
					continue;
				
				if($j==0)
				{
					$fldstr = "$fldstr, `$fld`";
				}
					
				if($val[0]=='@')
					{
					$val= substr($val,1);
					$ins .= ", $val";
					}
				else
					$ins .= ", '$val'";
				$i++;
			}
			$ins = "$ins)";
			$sql2 = "$sql2 $ins";
			$j++;
		}
		
		return $sql1.$fldstr.$sql2;
	}
	// query update
	function q_update($upd_data)
	{
		$sql = "UPDATE {$this->_PREFIX}{$upd_data['table']} SET ";
		$i = 0;
		$colcount=0;
		foreach($upd_data['data'] as $col => $val)
		{
			if(($col[0]=='/')||($col[0]=='\\')) // virtual field
				continue;
			
			if($val[0]=='@')	// mysql function calling
			{
				$val = sustr($val,1);
				$newelement = "`$col`=$val";
			}
			else 
				$newelement = "`$col`='$val'";
			
			if($i)
				$sql .= ", $newelement";
			else 
				$sql .= $newelement;
			$i++;
			$colcount++;
		}
		if($colcount==0) return FALSE;
		$sql = $sql." WHERE ".$this->makewhere($upd_data['where'], $this->_PREFIX) ;
		$this->dbg(__LINE__,$sql);
		return  $sql;
	}
	// Commit all bindings
	VAR $_BINDINGS = Array();
	function CommitBindings()
	{
		//var_dump($this->_BINDINGS);
		foreach($this->_BINDINGS as $b)
		{
	
			$this->create_binding($b['tblname'], $b['field'], $b['bind_data']);
			//echo "::DBCOMMIT::";
		}
		//echo "::DBCOMMIT::";
	}
	
	// Commit data table
	function CommitTable($tblname,$TableData)
	{
		//var_dump($TableData);
		if($this->existsTable($tblname))
		{
			// Change table structure
			
			$this->ChangeTable($tblname,$TableData);
		}
		else 
		{
			$this->CreateTable($tblname,$TableData);
		}
	}
	
	
}
?>
