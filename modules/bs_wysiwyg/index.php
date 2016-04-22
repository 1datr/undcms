<?php 

function on_bs_wysiwyg_crud_add_form_field($args)
{
	if($args['field']['Type']=='longtext')
	{
			
		$fldname=$args['fieldname'];
		$_name="row[$fldname]";
		
		$js_wysihtml5 = xbrotherfileurl(__FILE__, '/js/bootstrap3-wysihtml5.min.js');
		$js_handlebars = xbrotherfileurl(__FILE__, '/js/handlebars.runtime.min.js');
		$js_toolbar = xbrotherfileurl(__FILE__, '/js/wysihtml5x-toolbar.min.js');
		
		$css = xbrotherfileurl(__FILE__, '/css/bootstrap3-wysihtml5.min.css');
		$css2 = xbrotherfileurl(__FILE__, '/css/font-awesome.min.css');
		
		$args['html']="
				<?php 
				init_bootstrap();
				
				addjs(\"$js_toolbar\");
				addjs(\"$js_handlebars\");
				addjs(\"$js_wysihtml5\");
				
				
				addcss(\"$css\");
				addcss(\"$css2\");
			    jqready_gather(\"$('#inp_$fldname').wysihtml5({
    toolbar: {
      fa: true
    }
  });\")	
				?>
				<label for=\"inp_$fldname\" class=\"col-md-2 control-label\">$fldname</label>
				<div class=\"input-group col-md-5\">
				    <div class=\"btn-toolbar\" data-role=\"editor-toolbar\"
            data-target=\"#inp_$fldname\"></div> 
				<textarea id=\"inp_$fldname\" class=\"form-control\" rows=\"4\" name=\"$_name\" style=\"margin: 0px; height: 98px; width: 203px;\" placeholder=\"$fldname\"><?php echo last_post_val('$fldname'); ?></textarea>
				</div>";
	}
}
?>