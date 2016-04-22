<?php 

define(FLD_CREATE_DATE,'createdate');
// Расширение dbman
class DBMExtCreatedate extends DBMExtention{
	// event before adding new table
	function on_before_add_table($args)
	{
		/*
		 *   $args['scheme']
		 * */
		if(!xarray_key_exists(FLD_CREATE_DATE, $args))
		{
			$args['fields'][FLD_CREATE_DATE]='datetime';
		}
		
	}
	
	function on_prepare_def_data($args)
	{
		//var_dump($args);
		try
		{
			foreach ($args['defdata'] as $idx => &$dd)
			{
				//var_dump($ddi['defdata']);
				foreach ($dd as $j => &$ddj)
				{
					if(is_array($ddj))
					foreach ($ddj as $i => &$ddi)
					{
				//	var_dump($ddi);
					
					$ddi[FLD_CREATE_DATE]='@NOW()';
					}
				}
			}
		}
		catch (Exception $exc)
		{
			
		}
	}
	
	// event on query
	function on_before_query($args)
	{
		switch ($args['qmode'])
		{
			case 'select':
					$this->on_select($args);
				break;
			case 'update': 
					$this->on_update($args);
				break;
			case 'add': 
					$this->on_add($args);
				break;
			case 'delete': 
					$this->on_delete($args);
				break;
			case 'delitem': 
					$this->on_delitem($args);
				break;
		}
		
	}
	
	function on_select(&$args)
	{
		/*
		global $_CURR_LANGUAGE;
		if(is_array($args['args']['select']))
		foreach($args['args']['select'] as $idx => $val)
		{
			$matches = Array();
			// \ml:field
			if(preg_match_all("|[\\/]{0,1}ml\:(.+)\[(.+)\]|",$val,$matches))
			{				
					$fldname = $matches[1][0];		
					$lang = $matches[2][0];

					unset($args['scheme']->_SELECT_ARGS['select'][$idx]);
					$args['scheme']->_SELECT_ARGS['select'][]=$fldname."_".$lang;
					// delete the field
					unset($args['args']['select'][$idx]);
			}				
			// \ml:field[ru]
			elseif(preg_match_all("|[\\/]{0,1}ml\:(.+)|",$val,$matches))
			{
				$fldname = $matches[1][0];
				
				unset($args['scheme']->_SELECT_ARGS['select'][$idx]);
				$args['scheme']->_SELECT_ARGS['select'][]=$fldname."_".$_CURR_LANGUAGE;
			}
		}*/
	}
	// event after query
	function on_after_query(&$args)
	{
		/*
		switch ($args['qmode'])
		{
			case 'select':
				$this->aq_on_select($args);
				break;
			case 'update':
				$this->aq_on_update($args);
				break;
			case 'add':
				$this->aq_on_add($args);
				break;
			case 'delete':
				$this->aq_on_delete($args);
				break;
			case 'delitem':
				$this->aq_on_delitem($args);
				break;
		}
		*/
	}
	// after delete query
	function aq_on_delete($args)
	{
	
	}
	// after select query
	function aq_on_select($args)
	{
	
	}
	// make language cache
	VAR $_LANG=Array();
	function make_lang_table($args)
	{
	/*	//$this->_LANG = Array();
		if(count($this->_LANG)) return ;
		$_args = $args['scheme']->get_current_args(); // get the current args
		$_res = $args['scheme']->select('language',Array('id','short'))->exe();
		while($row = $args['scheme']->res_row($_res))
		{
			//var_dump($row);
			$this->_LANG[$row['short']]=$row['id'];
		}
		$args['scheme']->set_args($_args); // set the saved args
		*/
	}
	
	// after update 
	function aq_on_update($args)
	{
		
	}
	// after add query
	function aq_on_add($args)
	{
/*		global $_CURR_LANGUAGE;
		$this->make_lang_table($args);
		foreach($args['scheme']->_ADD_ARGS['data'] as $idx => $arr)
		{
			foreach ($arr as $key => $val)
			{
				$matches = Array();
				// \ml:field
				if(preg_match_all("|[\\/]{0,1}ml\:(.+)\[(.+)\]|",$key,$matches))
				{
					//	var_dump($matches);
			
					$fldname = $matches[1][0];
					$lang_descriptor = $matches[2][0];
					$tblname = $args['scheme']->_ADD_ARGS['table']."_$fldname";
					
					$_args = $args['scheme']->get_current_args(); // get the current args
					$args['scheme']->insert($tblname,Array(
								'recid'=>$args['qresult'][$idx],
								'lang'=>$this->_LANG[$lang_descriptor],
								'text'=>$val,
								)
							)->exe();
					//
					$args['scheme']->set_args($_args); // set the saved args
					
					//unset($args['args']['select'][$idx]);
				}
				// \ml:field[ru]
				elseif(preg_match_all("|[\\/]{0,1}ml\:(.+)|",$key,$matches))
				{
					$fldname = $matches[1][0];
					$lang_descriptor = $matches[2][0];
					$tblname = $args['scheme']->_ADD_ARGS['table']."_$fldname";
						
					$args['scheme']->insert($tblname,Array(
							'recid'=>$args['qresult'][$idx],
							'lang'=>$this->_LANG[$_CURR_LANGUAGE],
							'text'=>$val,
					)
					)->exe();
				}
			}
		}*/
	}
	// on delete item
	function aq_on_delitem($args)
	{
		
	}
	
	VAR $_UPDATED_ROWS=Array();
	
	function on_update(&$args)
	{
		/*
		global $_LANGS;
		foreach ($args['scheme']->_UPDATE_ARGS['data'] as $key => $val)
			{		
					$matches = Array();
					// \ml:field
					if(preg_match_all("|[\\/]{0,1}ml\:(.+)\[(.+)\]|",$key,$matches))
					{					
						$fldname = $matches[1][0];
						$lang_descriptor = $matches[2][0];
						if($lang_descriptor=="all")
						{
							foreach ($_LANGS as $lng => $linfo)
							{
								$thefield = $fldname."_".$lng;
								$args['scheme']->_UPDATE_ARGS['data'][$thefield]=$val;
							}
						}
						elseif(!empty($_LANGS[$lang_descriptor]))
						{						
							$thefield = $fldname."_".$lang_descriptor;
							$args['scheme']->_UPDATE_ARGS['data'][$thefield]=$val;
						}	
					//
					}
					// \ml:field[ru]
					elseif(preg_match_all("|[\\/]{0,1}ml\:(.+)|",$key,$matches))
					{
						$fldname = $matches[1][0];
						$thefield = $fldname."_".$_CURR_LANGUAGE;
						$args['scheme']->_UPDATE_ARGS['data'][$thefield]=$val;
							
					}
			}
			*/
	}
	
	function on_add(&$args)
	{
		global $_CURR_LANGUAGE;
		global $_LANGS;
		foreach($args['scheme']->_ADD_ARGS['data'] as $idx => $row)			
		{
			
			$args['scheme']->_ADD_ARGS['data'][$idx][FLD_CREATE_DATE]="@NOW()";
			
		}

	}
	
	function on_delete(&$args)
	{

	}
	
	function on_delitem(&$args)
	{
		
	}	
}
?>