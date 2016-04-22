<?php
		
		form_begin('crud/edit_problems');
		
		title("Edit problems #".$row['id']);
		?>
		<input type="hidden" name="row[id]" value="<?php echo $_QUERY['id']; ?>" />
		
						<div class="form-group">
						<label for="inp_name" class="col-md-2 control-label">name</label>
						<div class="input-group col-md-5">
						<input id="inp_name" type="text" class="form-control" name="row[name]" placeholder="name"  value="<?php echo $row['name']; ?>">
						</div>
						</div><div class="form-group">
		<?php
		init_bootstrap();

		addjs("/modules/tinymce/tinymce.min.js");

		jqready_gather("
		 
		tinyMCE.init({
		// General options
		mode : 'textareas',
		language : 'ru',
	 // skin : 'bootstrap',
	});
	 
	")
	?>
	<label for="inp_charact" class="col-md-2 control-label">charact</label>
	<div class="input-group col-md-8">
	<div class="btn-toolbar" data-role="editor-toolbar"
	data-target="#inp_charact"></div>
	<textarea id="inp_charact" class="form-control" rows="4" name="row[charact]" style="margin: 0px; height: 98px; width: 203px;" placeholder="charact">
	<?php echo $row['charact']; ?>
	</textarea>
	</div></div>
						<?php
						init_datetimepicker();
						?>
						
						<div class="form-group">
							
						<label for="dtp_inp_adate" class="col-md-2 control-label">adate</label>
							
						<div class="input-group date form_datetime col-md-5" data-date="1979-09-16T05:25:07Z" data-date-format="dd:mm:yyyy - HH:ii p" data-link-field="dtp_inp_adate">
						<input class="form-control" size="16" type="text" readonly>
						<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
						<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
						</div>
							
						<input type="hidden" name="row[adate]" value="<?php echo $row['adate']; ?>" id="dtp_inp_adate">
						</div>
				<?php
				$optres=$_DB->scheme->select('user','*')->exe();
					?>
					<div class="form-group">
						
					<label for="inp_autor" class="col-md-2 control-label">autor</label>
		
					<div class="input-group col-md-5">
					<select class="form-control" id="inp_autor" name="row[autor]">
					
				<?php
				while($optrow=$_DB->scheme->res_row($optres))
				{
				if( $row['autor']==$optrow['id'])
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