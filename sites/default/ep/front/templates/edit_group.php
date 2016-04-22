<?php
		
		form_begin('crud/edit_group');
		
		title("Edit group #".$row['id']);
		?>
		<input type="hidden" name="row[id]" value="<?php echo $_QUERY['id']; ?>" />
		
						<div class="form-group">
						<label for="inp_name" class="col-md-2 control-label">name</label>
						<div class="input-group col-md-5">
						<input id="inp_name" type="text" class="form-control" name="row[name]" placeholder="name"  value="<?php echo $row['name']; ?>">
						</div>
						</div>
				<?php
				$optres=$_DB->scheme->select('group','*')->exe();
					?>
					<div class="form-group">
						
					<label for="inp_parent" class="col-md-2 control-label">parent</label>
		
					<div class="input-group col-md-5">
					<select class="form-control" id="inp_parent" name="row[parent]">
					<option value="@Null">���</option>
				<?php
				while($optrow=$_DB->scheme->res_row($optres))
				{
				if( $row['parent']==$optrow['id'])
				{
				?><option value="<?php echo $optrow['id']; ?>" selected><?php echo template_parse($optrow,'{name} '); ?></option><?php
		
				}
				else
				{
						?><option value="<?php echo $optrow['id']; ?>"><?php echo template_parse($optrow,'{name} '); ?></option><?php
				}
				}
				?>
				</select>
				</div>
		
				</div>
						<div class="input-group input-group-sm">
						<input type="submit" class="btn btn-default" value="���������" />
						</div>
						<?php
						form_end();
		
?>