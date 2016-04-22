<?php
		
		form_begin('crud/edit_departments');
		
		title("Edit departments #".$row['id']);
		?>
		<input type="hidden" name="row[id]" value="<?php echo $_QUERY['id']; ?>" />
		
						<div class="form-group">
						<label for="inp_name" class="col-md-2 control-label">name</label>
						<div class="input-group col-md-5">
						<input id="inp_name" type="text" class="form-control" name="row[name]" placeholder="name"  value="<?php echo $row['name']; ?>">
						</div>
						</div>
				<?php
				$optres=$_DB->scheme->select('departments','*')->exe();
					?>
					<div class="form-group">
						
					<label for="inp_parent" class="col-md-2 control-label">parent</label>
		
					<div class="input-group col-md-5">
					<select class="form-control" id="inp_parent" name="row[parent]">
					
				<?php
				while($optrow=$_DB->scheme->res_row($optres))
				{
				if( $row['parent']==$optrow['id'])
				{
				?><option value="<?php echo $optrow['id']; ?>" selected><?php echo template_parse($optrow,'{}'); ?></option><?php
		
				}
				else
				{
						?><option value="<?php echo $optrow['id']; ?>"><?php echo template_parse($optrow,'{}'); ?></option><?php
				}
				}
				?>
				</select>
				</div>
		
				</div>
				<?php
				$optres=$_DB->scheme->select('user','*')->exe();
					?>
					<div class="form-group">
						
					<label for="inp_leader" class="col-md-2 control-label">leader</label>
		
					<div class="input-group col-md-5">
					<select class="form-control" id="inp_leader" name="row[leader]">
					
				<?php
				while($optrow=$_DB->scheme->res_row($optres))
				{
				if( $row['leader']==$optrow['id'])
				{
				?><option value="<?php echo $optrow['id']; ?>" selected><?php echo template_parse($optrow,'{login}'); ?></option><?php
		
				}
				else
				{
						?><option value="<?php echo $optrow['id']; ?>"><?php echo template_parse($optrow,'{login}'); ?></option><?php
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