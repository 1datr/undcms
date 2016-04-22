<?php 

// ��� ����� �������
function get_crud_view(&$_table,$tblname,&$DB)
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

function get_crud_view_tpl(&$_table,$tblname,&$DB)
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
	<h4>��� ������</h4>
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
	";
	return $html;
}


function get_crud_view_json(&$_table,$tblname,&$DB)
{
	$fld_to_pass=Array(FLD_CREATE_DATE);
	$akeys=array_keys($_table->_FIELDS);
	$akeys2=Array("'id'");
	foreach($_table->_FIELDS as $key => $fld)
	{
		if(in_array($key, $fld_to_pass))
			continue;
		$akeys2[]="'$key'";
	}
	$html="<?php
	if(!signature('page:integer|1'))
	error404();
	\$select=$arr_fields;
	\$_PSIZE=5;
	\$res=\$_DB->scheme->select('$tblname',$arr_fields)->page(\$_PSIZE,\$_QUERY['page'])->exe();
		while(\$row=\$_DB->scheme->res_row(\$res))
		{
		
		}
	
}
else
{
	
}
?>
</div>
<?php
\$pcount=\$_DB->scheme->last_select_pagecount(\$_PSIZE);
if(\$pcount>1)
{
	
}

";

	return $html;
}
// ��� ����� ��������������
function get_edit_form(&$_table,$tblname,&$DB)
{
	$fld_to_pass=Array(FLD_CREATE_DATE);
	$html="<?php 
	
	if(!signature('id:integer'))
	error404();
	\$res=\$_DB->scheme->select('$tblname','*')->where('id='.\$_QUERY['id'])->exe();
	\$row=\$_DB->scheme->res_row(\$res);
	if(!\$_DB->scheme->result_count(\$res))
	error404();
	use_template('edit_$tblname',Array('row'=>\$row));
	?>
	";
	
	return $html;
}
// ��� ����� ��������������
function get_edit_form_tpl(&$_table,$tblname,&$DB)
{
	exe_event('crud_edit_prepare_args', Array('DB'=>$DB));
	$fld_to_pass=Array(FLD_CREATE_DATE);
	
	$html="<?php

	form_begin('crud/edit_$tblname');
	
	title(\"Edit $tblname #\".\$row['id']);
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
// ��� �������� ��������������
function get_edit_action(&$_table,$tblname,&$DB)
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
// ��� ����� ����������
function get_add_form(&$_table,$tblname,&$DB)
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
use_template('add_{$tblname}',Array('res_lookup'=>\$res_lookup));
?>";
	return $html;
}
// ��� ����� ����������
function get_add_form_tpl(&$_table,$tblname,&$DB)
{
	$fld_to_pass=Array(FLD_CREATE_DATE);
	$html="<?php
	form_begin('crud/add_$tblname');
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
	<input type=\"submit\" class=\"btn btn-default\" value=\"���������\" />
</div>
<?php
form_end();
?>";

	return $html;
}
// ��� �������� ����������
function get_add_action(&$_table,$tblname,&$DB)
{
	
	$html="<?php
			\$_DB->scheme->insert('$tblname',\$_POST['row'])->exe();
			?>";
	
	return $html;
}
// ��� �������� ��������
function get_delete_action(&$_table,$tblname,&$DB)
{
	$html="<?php
	if(!empty(\$_POST['id']))
	{
		\$_DB->scheme->delete_item('$tblname',\$_POST['id'])->exe();
	}		
			?>";
	
	return $html;
}
// ��� ����� ��������
function get_delete_form(&$_table,$tblname,&$DB,$rowvar='$row')
{
	$html="<?php
form_begin('crud/delete_$tblname',Array('class'=>'frm_delete_$tblname','confirm'=>'Are you realy want to delete this item?'));
?>
			<input type=\"hidden\" name=\"id\" value=\"<?php echo \$_PARAMS['ID']; ?>\" />
			<input type=\"submit\" class=\"btn btn-default\" name=\"delete\" value=\"Delete\" />
<?php
form_end();			
?>";	
	return $html;
}
?>
