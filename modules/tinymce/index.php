<?php 

function on_tinymce_crud_add_form_field($args)
{
	if($args['field']['Type']=='longtext')
	{
			
		$fldname=$args['fieldname'];
		$_name="row[$fldname]";
		
		$js_tinymce_jq = xbrotherfileurl(__FILE__, '/jquery.tinymce.min.js');
		$js_tinymce = xbrotherfileurl(__FILE__, '/tinymce.min.js');
		
		//$css = xbrotherfileurl(__FILE__, '/css/bootstrap3-wysihtml5.min.css');

		//$args['prefix']='<div class="input-group col-md-8">';
		$args['html']="
				<?php 
				init_bootstrap();
				
				addjs(\"$js_tinymce\");				

			    jqready_gather(\"
			    
			    tinyMCE.init({
      // General options
      mode : 'textareas',
	  language : 'ru',	   
	 // skin : 'bootstrap',    		 		
   });
			    
			    \")	
				?>
				<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
				<div class=\"input-group col-md-8\">
				    <div class=\"btn-toolbar\" data-role=\"editor-toolbar\"
            data-target=\"#inp_$fldname\"></div> 
				<textarea id=\"inp_$fldname\" class=\"form-control\" rows=\"4\" name=\"$_name\" style=\"margin: 0px; height: 98px; width: 203px;\" placeholder=\"$fldname\"><?php echo last_post_val('$fldname'); ?></textarea>
				</div>";
	}
}

function on_tinymce_crud_edit_form_field($args)
{
	if($args['field']['Type']=='longtext')
	{
			
		$fldname=$args['fieldname'];
		$_name="row[$fldname]";

		$js_tinymce_jq = xbrotherfileurl(__FILE__, '/jquery.tinymce.min.js');
		$js_tinymce = xbrotherfileurl(__FILE__, '/tinymce.min.js');

		//$css = xbrotherfileurl(__FILE__, '/css/bootstrap3-wysihtml5.min.css');

		//$args['prefix']='<div class="input-group col-md-8">';
		$args['html']="
		<?php
		init_bootstrap();

		addjs(\"$js_tinymce\");

		jqready_gather(\"
		 
		tinyMCE.init({
		// General options
		mode : 'textareas',
		language : 'ru',
	 // skin : 'bootstrap',
	});
	 
	\")
	?>
	<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
	<div class=\"input-group col-md-8\">
	<div class=\"btn-toolbar\" data-role=\"editor-toolbar\"
	data-target=\"#inp_$fldname\"></div>
	<textarea id=\"inp_$fldname\" class=\"form-control\" rows=\"4\" name=\"$_name\" style=\"margin: 0px; height: 98px; width: 203px;\" placeholder=\"$fldname\">
	<?php echo \$row['$fldname']; ?>
	</textarea>
	</div>";
	}
}
?>