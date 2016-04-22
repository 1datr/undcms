<?php
	form_begin('crud/add_pmessage');
	?>
				<div class="form-group">
				<label for="inp_title" class="col-md-2 control-label">title</label>
				<div class="input-group col-md-5">
				<input id="inp_title" type="text" class="form-control" name="row[title]" placeholder="title" value="<?php echo last_post_val('title'); ?>">
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
				<label for="inp_content" class="col-md-2 control-label">content</label>
				<div class="input-group col-md-8">
				    <div class="btn-toolbar" data-role="editor-toolbar"
            data-target="#inp_content"></div> 
				<textarea id="inp_content" class="form-control" rows="4" name="row[content]" style="margin: 0px; height: 98px; width: 203px;" placeholder="content"><?php echo last_post_val('content'); ?></textarea>
				</div></div>
				<?php
				?>
				<div class="form-group">
					
				<label for="inp_from" class="col-md-2 control-label">from</label>
			
				<div class="input-group col-md-5">
				<select class="form-control" id="inp_from" name="row[from]">
				
				<?php
				while($row=$_DB->scheme->res_row($res_lookup[from]))
				{
				?><option value="<?php echo $row['id']; ?>"><?php echo template_parse($row,'{login} '); ?></option><?php
			}
			?>
			</select>
			</div>
			
			</div>
				<?php
				?>
				<div class="form-group">
					
				<label for="inp_to" class="col-md-2 control-label">to</label>
			
				<div class="input-group col-md-5">
				<select class="form-control" id="inp_to" name="row[to]">
				
				<?php
				while($row=$_DB->scheme->res_row($res_lookup[to]))
				{
				?><option value="<?php echo $row['id']; ?>"><?php echo template_parse($row,'{login} '); ?></option><?php
			}
			?>
			</select>
			</div>
			
			</div>
				<?php
				init_datetimepicker();
				?>
				
				<div class="form-group">
			
				<label for="dtp_inp_date" class="col-md-2 control-label">date</label>
			
				<div class="input-group date form_datetime col-md-5" data-date="1979-09-16T05:25:07Z" data-date-format="dd:mm:yyyy - HH:ii p" data-link-field="dtp_inp_date">
				<input class="form-control" size="16" type="text" readonly>
				<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
				<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
				</div>
			
				<input type="hidden" name="row[date]" id="dtp_inp_date">
				</div>
<div class="input-group input-group-sm">
	<input type="submit" class="btn btn-default" value="[t@Add]" />
</div>
<?php
form_end();
?>