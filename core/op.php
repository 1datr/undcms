<?php 
// класс с информацией об операции
class OPInfo 
{
	VAR $generator;
	VAR $model;
	
	function __construct($model=null,$generator='standart')
	{
		$this->generator=$generator;
		$this->model=$model;
	}
	
}

$op_std_view=Array(
		'views'=>Array('base_view'=>Array('page'=>'crud/*')),
	);

$op_std_add=Array(
		'forms'=>Array('add_form'),
		'actions'=>Array('add_action'),
);

$op_std_custom_op=Array(
		'model'=>Array(),
		'generator'=>'standart',
		'#primitives'=>Array(
			'forms'=>Array('{con}/{op}'),
			'actions'=>Array('{con}/{op}'),
			'template'=>Array('{con}_{op}'),
		),	
		'#noerase'=>true,
);

$op_std_edit=Array(
		'forms'=>Array('edit_form'=>Array('page'=>'crud/*')),
		'actions'=>Array('edit_action'),
);

$op_std_delete=Array(
		'forms'=>Array('delete_form'),
		'actions'=>Array('delete_action'),
);

$def_addata=Array(
	'#op'=>Array(
		'front'=>Array(
			'view'=>Array('model'=>$op_std_view,'generator'=>'standart'),
			'add'=>Array('model'=>$op_std_add,'generator'=>'standart'),
			'edit'=>Array('model'=>$op_std_edit,'generator'=>'standart'),
			'delete'=>Array('model'=>$op_std_delete,'generator'=>'standart'),
			),
		'admin'=>Array(
			'view'=>Array('model'=>$op_std_view,'generator'=>'standart'),
			'add'=>Array('model'=>$op_std_add,'generator'=>'standart'),
			'edit'=>Array('model'=>$op_std_edit,'generator'=>'standart'),
			'delete'=>Array('model'=>$op_std_delete,'generator'=>'standart'),
			),
		)	
);

function attach_con(&$data,$conkey,&$con_buf)
{
	
}

class ConInfo 
{
	VAR $oplist;
	VAR $mode;
	VAR $name;
	VAR $primitives;
	
	function __construct($info=null,$table='')
	{		
		global $def_addata;
		if(empty($info))
			$info=Array('#op'=>Array());
		
		if(xarray_key_exists('conname', $info))
			$this->name=$info['conname'];
		else 
			$this->name=$table."s";
		
		xdefarray($def_addata, $info);
		$this->oplist=$info['#op'];
		if(xarray_key_exists('#primitives', $info))
			$this->primitives=$info['#primitives'];
		$this->settable($table);
	}
	
	function settable($tbl)
	{
		global $_DB;
		foreach ($this->oplist as $_ep => &$_for_ep)
		{
			foreach ($_for_ep as $key => &$item)
			{
				if(!xarray_key_exists('table', $item))
				{
					$item['table']=&$_DB->scheme->_SCHEME[$tbl];
					$item['tablename']=$tbl;
				}
			}
		}
	}
	
	function get_primitives()
	{
		$reskeys=Array('views','forms','actions','pages','templates','blocks');
		$xres = Array();
		foreach ($reskeys as $_key)
		{
			$xres[$_key]=Array();
		}
		
		foreach ($this->oplist as $_the_ep => &$_for_ep)
		{
			
			foreach ($_for_ep as $op => &$opinfo)
			{
				xdefarray(Array('generator'=>'standart'), $opinfo);
				$gen_class_name="cgenerator_".$opinfo['generator'];
				
				$gen_obj=new $gen_class_name();
				$op_method_name="op_".$op;
				if(!method_exists($gen_obj, $op_method_name))
				{
					$op_method_name="op_custom";
				}
				// результаты от генератора
				$gen_obj->_EP=$_the_ep;
				$gen_obj->_CON=$this->name;
				$gen_obj->_OP=$op;
				$gen_obj->setdirs();
				
				$gen_obj->prepare($opinfo);
				$res = $gen_obj->$op_method_name($opinfo);
			
				foreach ($reskeys as $_key)
				{
					if(xarray_key_exists($_key, $res))
					foreach ($res[$_key] as $fname => $code)
					{
						$xres[$_key][$fname] = $code;
					}
				}
			}
		}
		return $xres;
	}
	
	// добавить контроллер
	function attach($attaching)
	{
		if(get_class($attaching)=='ConInfo')
		{
			foreach ($attaching->oplist as $_the_ep => &$for_ep)
			{
				foreach ($for_ep as $op_key => &$op)
				{
					if(empty($this->oplist[$_the_ep]))
						$this->oplist[$_the_ep]=Array();
					$this->oplist[$_the_ep][$op['tablename'].".".$op_key]=$op;
				}
			}
		}
	}
}

// базовый класс менеджер скаффолдинга
class cgenerator 
{
	VAR $_EP;
	VAR $_CON;
	VAR $_OP;
	
	function setdirs()
	{
		global $_BASE_PATH,$_SITE;
		$this->crud_acts =$_BASE_PATH."/sites/$_SITE/ep/{$this->_EP}/actions";
		$this->crud_forms =$_BASE_PATH."/sites/$_SITE/ep/{$this->_EP}/forms";
		$this->crud_views =$_BASE_PATH."/sites/$_SITE/ep/{$this->_EP}/views";
		$this->templates_dir =$_BASE_PATH."/sites/$_SITE/ep/{$this->_EP}/templates";
		$this->pages_dir =$_BASE_PATH."/sites/$_SITE/ep/{$this->_EP}/pages";
	}

	function prepare(&$buf)
	{
		foreach ($buf as $key => &$item)
		{
			if(is_array($item))
			{
				$this->prepare($item);
			}
			elseif(is_string($item)) 
			{
				$item=strtr($item,Array('{'.'op'.'}'=>$this->_OP,'{'.'con'.'}'=>$this->_CON));
			}
		}
	}
	
	function op_custom($opinfo)
	{
		$r=2;
		$res=Array();
		if(xarray_key_exists('forms', $opinfo['#primitives']))
		{
			$res['forms']=Array();
			foreach ($opinfo['#primitives']['forms'] as $key)
			{
				$res['forms'][$this->crud_forms."/".$key]="
<?php 

?>						
		";
			}
		}
		if(xarray_key_exists('views', $opinfo['#primitives']))
		{	
			$res['views']=Array();
			foreach ($opinfo['#primitives']['views'] as $key )
			{
				$res['views'][$this->crud_views."/".$key]="
<?php
			
?>
		";
			}
			
		}
		if(xarray_key_exists('templates', $opinfo['#primitives']))
		{
			$res['templates']=Array();
			foreach ($opinfo['#primitives']['templates'] as $key )
			{
				$res['templates'][$this->templates_dir."/".$key]="
<?php
		
?>
		";
			}
				
		}
		if(xarray_key_exists('pages', $opinfo['#primitives']))
		{
			$res['pages']=Array();
			foreach ($opinfo['#primitives']['pages'] as $key )
			{
				$res['pages'][$this->pages_dir."/".$key]="
<?php
		
?>
		";
			}
		
		}
		if(xarray_key_exists('actions', $opinfo['#primitives']))
		{
			$res['actions']=Array();
			foreach ($opinfo['#primitives']['pages'] as $key )
			{
				$res['actions'][$this->crud_acts."/".$key]="
<?php
		
?>
		";
			}
		
		}
		return $res;
	}
}

function  make_view($ep,$viewname)
{
	
}

class cgenerator_standart extends cgenerator 
{
	
	function code_template_view($_table,$tblname)
	{
			$fld_to_pass=Array(FLD_CREATE_DATE);
			$akeys=array_keys($_table->_FIELDS);
			$akeys2=Array("'id'");
			$ths=Array('<th>id</th>');
			$tds=Array("<td><?php echo \$row['id']; ?></td>");
			foreach($_table->_FIELDS as $key => $fld)
			{
				if(in_array($key, $fld_to_pass))
					continue;
				$akeys2[]="'$key'";
				$ths[]="<th>$key</th>";
				if(xarray_key_exists('bind',$fld))
				{
			
					$lu_fields=get_template_vars($fld['bind']['lookuptemplate']);
					$_tpl=Array();
					foreach($lu_fields as $tfield)
					{
						$tvar = "_".$key."_".$tfield;
						$_tpl["{".$tfield."}"]="{".$tvar."}";
					}
					$temp_lookup=strtr($fld['bind']['lookuptemplate'],$_tpl);
					$tds[]="<td><?php echo template_parse(\$row,'".$temp_lookup."'); ?></td>";
						
			
				}
				else
				{
						
					if(in_array($fld['Type'],Array('longtext')))
						$tds[]="<td><?php echo nl2br(truncstr(\$row['$key'],30)); ?></td>";
					else
						$tds[]="<td><?php echo \$row['$key']; ?></td>";
				}
			
			}
			
			$ths[]="<th></th><th></th>";
			$tds[]="<td><a href=\"<?php echo url(\"{$tblname}s/edit/\".\$row['id']); ?>\"><button type=\"button\" class=\"btn btn-default\">Edit</button></a></td>";
			$tds[]="<td><?php echo get_form('/crud/delete_$tblname',Array('ID'=>\$row['id'])); ?></td>";
			$arr_fields='Array('.implode(',',$akeys2).')';
			$html="
			<div class=\"table-responsive\">
			<?php
			title('{$tblname}s');
			if(\$_DB->scheme->result_count(\$res))
			{
			
			?>
			<table class=\"table table-striped\">
			<tr>
			".implode('',$ths)."
		</tr>
		<?php
			
			while(\$row=\$_DB->scheme->res_row(\$res))
			{
			?>
			<tr>".
					implode('', $tds).
					"	</tr>
				<?php
		}
		?>
		</table>
		<?php
		}
		else
		{
		?>
		<h4>[t@No data]</h4>
		<?php
		}
		?>
		</div>
		<?php
		if(\$pcount>1)
		{
		?>
		<ul class=\"pagination\">
		<?php
		for(\$p=1;\$p<=\$pcount;\$p++)
		{
		\$theclass='';
		if(\$p==\$_QUERY['page'])
		\$theclass=' class=\"active\"';
		?>
		<li <?php echo \$theclass; ?>><a href=\"<?php echo url(\"\$_PAGE/page:\$p\"); ?>/\"><?php echo \$p; ?></a></li>
		<?php
		}
		?>
		</ul>
		<?php
		}
		/* VIEW TEMPLATE END */
		";
		return $html;
	}
	
	function code_view($_table,$tblname)
	{
		$fld_to_pass=Array(FLD_CREATE_DATE);
		$akeys=array_keys($_table->_FIELDS);		
		
		$akeys2=Array("'id'"); // ������ �����, ��������� ��������
		$ths=Array('<th>id</th>');
		$tds=Array("<td><?php echo \$row['id']; ?></td>");
		$the_keys=Array();
		foreach($_table->_FIELDS as $key => $fld)
		{
			if(in_array($key, $fld_to_pass))
				continue;
			$akeys2[]="'$key'";
			if(xarray_key_exists('bind',$fld))
			{
					
				$lu_fields=get_template_vars($fld['bind']['lookuptemplate']);
		
				foreach($lu_fields as $idx => $lu_fld)
				{
					$akeys2[]="'$key|$lu_fld'";
				}
		
			}
			$the_keys[$key]=$fld;
		}
		exe_event('crud_view_prepare_args', Array('fields_to_select'=>&$akeys2,'fields_to_out'=>&$the_keys));
		foreach($the_keys as $key => $fld)
		{
			$ths[]="<th>$key</th>";
			if(in_array($fld['Type'],Array('longtext')))
				$tds[]="<td><?php echo nl2br(truncstr(\$row['$key'],30)); ?></td>";
			else
				$tds[]="<td><?php echo \$row['$key']; ?></td>";
		
		}
		
		
		
		$ths[]="<th></th><th></th>";
		$tds[]="<td><a href=\"<?php echo url(\"{$tblname}s/edit/\".\$row['id']); ?>\"><button type=\"button\" class=\"btn btn-default\">Edit</button></a></td>";
		$tds[]="<td><?php echo get_form('/crud/delete_$tblname',Array('ID'=>\$row['id'])); ?></td>";
		$arr_fields='Array('.implode(',',$akeys2).')';
		$html="<?php
		if(!signature('page:integer|1'))
		error404();
		\$select=$arr_fields;
		\$_PSIZE=5;
		\$res=\$_DB->scheme->select('$tblname',\$select)->page(\$_PSIZE,\$_QUERY['page'])->exe();
		\$pcount=\$_DB->scheme->last_select_pagecount(\$_PSIZE);
		use_template(\"view_{$tblname}\",Array('res'=>\$res,'pcount'=>\$pcount));
		
		";
		
		return $html;
	}
	
	
	
	function op_view($opinfo)
	{
		if(xarray_key_exists('table', $opinfo))
		{
			$_table=$opinfo['table'];
			$_tablename=$opinfo['tablename'];
		}
		else
		{
			
		}
		if($this->_EP=='admin')
		{
			return Array(
					'views'=>Array("{$this->crud_views}/crud/{$_tablename}"=>$this->code_view($_table,$_tablename)),
					'templates'=>Array("{$this->templates_dir}/view_{$_tablename}"=>$this->code_template_view($_table,$_tablename)),
					'pages'=>Array("{$this->pages_dir}/{$_tablename}s/index"=>"
							<?php
							echo get_view('crud/{$_tablename}');
							echo get_form('crud/add_{$_tablename}');
							?>")
					);
		}
		else 
		{
			return Array(
					'views'=>Array("{$this->crud_views}/crud/{$_tablename}"=>$this->code_view($_table,$_tablename)),
					'templates'=>Array("{$this->templates_dir}/view_{$_tablename}"=>$this->code_template_view($_table,$_tablename)),	
					'pages'=>Array("{$this->pages_dir}/{$_tablename}s/index"=>"
							<?php
							echo get_view('crud/{$_tablename}');
							?>")
			);
		}
	}
	
	function code_template_add_form($_table,$_tblname)
	{
		$fld_to_pass=Array(FLD_CREATE_DATE);
		$html="<?php
	form_begin('crud/add_$_tblname');
	?>";
	foreach($_table->_FIELDS as $fldname => $fld)
	{
		if(in_array($fldname, $fld_to_pass))
			continue;
		$_name="row[$fldname]";
		$fldhtml='';
		$fld_prefix='<div class="form-group">';
		$fld_suffix='</div>';
		exe_event('crud_add_form_field', Array(
				'field'=>$fld,
				'fieldname'=>$fldname,
				'html'=>&$fldhtml,
				'prefix'=>&$fld_prefix,
				'suffix'=>&$fld_suffix,
				));
		if($fldhtml=='')
		{
			if($fld['bind']!=null)
			{
				$null_opt ="";
				$lookup ="<?php echo template_parse(\$row,'". $fld['bind']['lookuptemplate']. "'); ?>";
				if($fld['Null']=='YES')
					$null_opt="<option value=\"@Null\">���</option>";
				$html=$html."
				<?php
				?>
				$fld_prefix
					
				<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
			
				<div class=\"input-group col-md-5\">
				<select class=\"form-control\" id=\"inp_$fldname\" name=\"$_name\">
				$null_opt
				<?php
				while(\$row=\$_DB->scheme->res_row(\$res_lookup[$fldname]))
				{
				?><option value=\"<?php echo \$row['". $fld['bind']['field_to']. "']; ?>\">$lookup</option><?php
			}
			?>
			</select>
			</div>
			
			$fld_suffix";
			}
			elseif($fld['Type']=='datetime')
			{
				$html=$html."
				<?php
				init_datetimepicker();
				?>
				";
				$html=$html."
				$fld_prefix
			
				<label for=\"dtp_inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
			
				<div class=\"input-group date form_datetime col-md-5\" data-date=\"1979-09-16T05:25:07Z\" data-date-format=\"dd:mm:yyyy - HH:ii p\" data-link-field=\"dtp_inp_$fldname\">
				<input class=\"form-control\" size=\"16\" type=\"text\" readonly>
				<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-remove\"></span></span>
				<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-th\"></span></span>
				</div>
			
				<input type=\"hidden\" name=\"$_name\" id=\"dtp_inp_$fldname\">
				$fld_suffix";
			
			}
			elseif($fld['Type']=='longtext')
			{
				$html=$html."
				$fld_prefix
				<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
				<div class=\"input-group col-md-5\">
				<textarea id=\"inp_$fldname\" class=\"form-control\" rows=\"4\" name=\"$_name\" style=\"margin: 0px; height: 98px; width: 100%;\" placeholder=\"$fldname\"><?php echo last_post_val('$fldname'); ?></textarea>
				</div>
				$fld_suffix";
			}
			else
			{
				$html=$html."
				$fld_prefix
				<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
				<div class=\"input-group col-md-5\">
				<input id=\"inp_$fldname\" type=\"text\" class=\"form-control\" name=\"$_name\" placeholder=\"$fldname\" value=\"<?php echo last_post_val('$fldname'); ?>\">
				</div>
				$fld_suffix";
			
			}
		}
		else 
		{
			$html=$html.$fld_prefix.$fldhtml.$fld_suffix;
		}
		
	}
	$html=$html."
<div class=\"input-group input-group-sm\">
	<input type=\"submit\" class=\"btn btn-default\" value=\"[t@Add]\" />
</div>
<?php
form_end();
?>";

	return $html;
	}
	
	function code_add_form($_table,$_tblname)
	{
		$fld_to_pass=Array(FLD_CREATE_DATE);
		$html="";
		foreach($_table->_FIELDS as $fldname => $fld)
		{
			$_name="row[$fldname]";
			if($fld['bind']!=null)
			{
				$html=$html."
				<?php
				\$res_lookup['$fldname']=\$_DB->scheme->select('". $fld['bind']['table_to']. "','*')->exe();
				?>
				";
			}
		
		}
		
		$html=$html."<?php
		use_template('add_{$_tblname}',Array('res_lookup'=>\$res_lookup));
		/* ADD FORM END */
		?>";
		return $html;
	}
	
	function code_add_action(&$_table,$tblname)
	{
		$html="<?php
			\$_DB->scheme->insert('$tblname',\$_POST['row'])->exe();
			?>";
	
		return $html;
			
	}
	
	function op_add($opinfo)
	{
		$_table=$opinfo['table'];
		$_tablename=$opinfo['tablename'];
		
		return Array(
					'templates'=>Array("{$this->templates_dir}/add_{$_tablename}"=>$this->code_template_add_form($_table,$_tablename)),
					'forms'=>Array("{$this->crud_forms}/crud/add_{$_tablename}"=>$this->code_add_form($_table,$_tablename)),
					'actions'=>Array("{$this->crud_acts}/crud/add_{$_tablename}"=>$this->code_add_action($_table,$_tablename)),
		);
	}
	
	function code_edit_form($_table,$_tblname)
	{
		$fld_to_pass=Array(FLD_CREATE_DATE);
		$html="<?php
		
		if(!signature('id:integer'))
		error404();
		\$res=\$_DB->scheme->select('$_tblname','*')->where('id='.\$_QUERY['id'])->exe();
		\$row=\$_DB->scheme->res_row(\$res);
		if(!\$_DB->scheme->result_count(\$res))
		error404();
		use_template('edit_$_tblname',Array('row'=>\$row));
				?>
				";
	
		return $html;
	}
	
	function code_template_edit_form($_table,$_tablename)
	{
		exe_event('crud_edit_prepare_args', Array('DB'=>$DB));
		$fld_to_pass=Array(FLD_CREATE_DATE);
		
		$html="<?php
		
		form_begin('crud/edit_$_tablename');
		
		title(\"Edit $_tablename #\".\$row['id']);
		?>
		<input type=\"hidden\" name=\"row[id]\" value=\"<?php echo \$_QUERY['id']; ?>\" />
		";
		foreach($_table->_FIELDS as $fldname => $fld)
		{
		if(in_array($fldname, $fld_to_pass))
			continue;
			$fldhtml='';
			$fld_prefix='<div class="form-group">';
		$fld_suffix='</div>';
		exe_event('crud_edit_form_field', Array(
						'field'=>$fld,
						'fieldname'=>$fldname,
						'html'=>&$fldhtml,
						'prefix'=>&$fld_prefix,
						'suffix'=>&$fld_suffix,
				));
		
				$_name="row[$fldname]";
				if($fldhtml=='')
				{
				if($fld['bind']!=null)
				{
				$null_opt ="";
				$lookup ="<?php echo template_parse(\$optrow,'". $fld['bind']['lookuptemplate']. "'); ?>";
						if($fld['Null']=='YES')
								$null_opt="<option value=\"@Null\">���</option>";
				$html=$html."
				<?php
				\$optres=\$_DB->scheme->select('". $fld['bind']['table_to']. "','*')->exe();
					?>
					<div class=\"form-group\">
						
					<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
		
					<div class=\"input-group col-md-5\">
					<select class=\"form-control\" id=\"inp_$fldname\" name=\"$_name\">
					$null_opt
				<?php
				while(\$optrow=\$_DB->scheme->res_row(\$optres))
				{
				if( \$row['$fldname']==\$optrow['id'])
				{
				?><option value=\"<?php echo \$optrow['". $fld['bind']['field_to']. "']; ?>\" selected>$lookup</option><?php
		
				}
				else
				{
						?><option value=\"<?php echo \$optrow['". $fld['bind']['field_to']. "']; ?>\">$lookup</option><?php
				}
				}
				?>
				</select>
				</div>
		
				</div>";
				}
				elseif($fld['Type']=='datetime')
				{
						$html=$html."
						<?php
						init_datetimepicker();
						?>
						";
						$html=$html."
						<div class=\"form-group\">
							
						<label for=\"dtp_inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
							
						<div class=\"input-group date form_datetime col-md-5\" data-date=\"1979-09-16T05:25:07Z\" data-date-format=\"dd:mm:yyyy - HH:ii p\" data-link-field=\"dtp_inp_$fldname\">
						<input class=\"form-control\" size=\"16\" type=\"text\" readonly>
						<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-remove\"></span></span>
						<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-th\"></span></span>
						</div>
							
						<input type=\"hidden\" name=\"$_name\" value=\"<?php echo \$row['$fldname']; ?>\" id=\"dtp_inp_$fldname\">
						</div>";
		
				}
				elseif($fld['Type']=='longtext')
				{
				$html=$html."
				<div class=\"form-group\">
				<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
				<div class=\"input-group col-md-5\">
				<textarea id=\"inp_$fldname\" class=\"form-control\" rows=\"4\" name=\"$_name\" style=\"margin: 0px; height: 98px; width: 100%;\" placeholder=\"$fldname\">
				<?php echo \$row['$fldname']; ?>
				</textarea>
				</div>
				</div>";
						}
						else
						{
						$html=$html."
						<div class=\"form-group\">
						<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
						<div class=\"input-group col-md-5\">
						<input id=\"inp_$fldname\" type=\"text\" class=\"form-control\" name=\"$_name\" placeholder=\"$fldname\"  value=\"<?php echo \$row['$fldname']; ?>\">
						</div>
						</div>";
		
						}
						}
						else
							{
							$html=$html.$fld_prefix.$fldhtml.$fld_suffix;
						}
						}
						$html=$html."
						<div class=\"input-group input-group-sm\">
						<input type=\"submit\" class=\"btn btn-default\" value=\"���������\" />
						</div>
						<?php
						form_end();
		
?>";
		
	return $html;
	}
	
	function code_edit_action(&$_table,$tblname)
	{
		$fld_to_pass=Array(FLD_CREATE_DATE);
		$html="<?php
		//var_dump(\$_POST);
		\$id=\$_POST['row']['id'];
		unset(\$_POST['row']['id']);
		\$_DB->scheme->update('$tblname',\$_POST['row'])->where(\"id=\$id\")->exe();
		?>";
	
		return $html;
			
	}
	
	function op_edit($opinfo)
	{
		$_table=$opinfo['table'];
		$_tablename=$opinfo['tablename'];
		
		return Array(
				'templates'=>Array("{$this->templates_dir}/edit_{$_tablename}"=>$this->code_template_edit_form($_table,$_tablename)),
				'forms'=>Array("{$this->crud_forms}/crud/edit_{$_tablename}"=>$this->code_edit_form($_table,$_tablename)),
				'actions'=>Array("{$this->crud_acts}/crud/edit_{$_tablename}"=>$this->code_edit_action($_table,$_tablename)),
				'pages'=>Array("{$this->pages_dir}/{$_tablename}s/edit"=>"
						<?php
						echo get_form('crud/edit_{$_tablename}');
						?>"
					),
				);
	}
	
	function code_delete_action($_table,$_tblname)
	{
	
	}

	function code_delete_form($_table,$_tblname)
	{
		
	}
	
	function op_delete($opinfo)
	{
		$_table=$opinfo['table'];
		$_tablename=$opinfo['tablename'];
		return Array(
				'templates'=>Array("{$this->templates_dir}/delete_{$_tablename}"=>"
<input type=\"hidden\" name=\"id\" value=\"<?php echo \$_PARAMS['ID']; ?>\" />
<input type=\"submit\" class=\"btn btn-default\" name=\"delete\" value=\"Delete\" />
"),
				'forms'=>Array("{$this->crud_forms}/crud/delete_{$_tablename}"=>"
<?php
form_begin('crud/delete_$tablename',Array('class'=>'frm_delete_$tablename','confirm'=>'Are you realy want to delete this item?'));
use_template('delete_$tablename',Array('row'=>\$row));
form_end();			
?>						
"),
				'actions'=>Array("{$this->crud_acts}/crud/delete_{$_tablename}"=>"
<?php
	if(!empty(\$_POST['id']))
	{
		\$_DB->scheme->delete_item('{$_tablename}',\$_POST['id'])->exe();
	}		
			?>"),
		);
	}
	

	
}

?>