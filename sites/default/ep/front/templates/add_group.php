<?php
	form_begin('crud/add_group');
	?>
				<div class="form-group">
				<label for="inp_name" class="col-md-2 control-label">name</label>
				<div class="input-group col-md-5">
				<input id="inp_name" type="text" class="form-control" name="row[name]" placeholder="name" value="<?php echo last_post_val('name'); ?>">
				</div>
				</div>
				<?php
				?>
				<div class="form-group">
					
				<label for="inp_parent" class="col-md-2 control-label">parent</label>
			
				<div class="input-group col-md-5">
				<select class="form-control" id="inp_parent" name="row[parent]">
				<option value="@Null">���</option>
				<?php
				while($row=$_DB->scheme->res_row($res_lookup[parent]))
				{
				?><option value="<?php echo $row['id']; ?>"><?php echo template_parse($row,'{name} '); ?></option><?php
			}
			?>
			</select>
			</div>
			
			</div>
<div class="input-group input-group-sm">
	<input type="submit" class="btn btn-default" value="[t@Add]" />
</div>
<?php
form_end();
?>