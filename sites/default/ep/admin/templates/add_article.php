<?php
	form_begin('crud/add_article');
	?>
				<div class="form-group">
				<label for="inp_name" class="col-md-2 control-label">name</label>
				<div class="input-group col-md-5">
				<input id="inp_name" type="text" class="form-control" name="row[name]" placeholder="name" value="<?php echo last_post_val('name'); ?>">
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
				<label for="inp_atext" class="col-md-2 control-label">atext</label>
				<div class="input-group col-md-8">
				    <div class="btn-toolbar" data-role="editor-toolbar"
            data-target="#inp_atext"></div> 
				<textarea id="inp_atext" class="form-control" rows="4" name="row[atext]" style="margin: 0px; height: 98px; width: 203px;" placeholder="atext"><?php echo last_post_val('atext'); ?></textarea>
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
			
				<input type="hidden" name="row[adate]" id="dtp_inp_adate">
				</div>
				<?php
				?>
				<div class="form-group">
					
				<label for="inp_user" class="col-md-2 control-label">user</label>
			
				<div class="input-group col-md-5">
				<select class="form-control" id="inp_user" name="row[user]">
				
				<?php
				while($row=$_DB->scheme->res_row($res_lookup[user]))
				{
				?><option value="<?php echo $row['id']; ?>"><?php echo template_parse($row,'{login}'); ?></option><?php
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