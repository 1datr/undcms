<?php

function fileupload_block($addparams)
{
	jqinit();
	addjs(xbrotherfileurl(__FILE__,'/js/jquery.form.min.js'));
	addjs(xbrotherfileurl(__FILE__,'/js/jquery.uploadfile.min.js'));
	addcss(xbrotherfileurl(__FILE__,'/css/uploadfile.min.css'));
	xdefarray(Array('multiple'=>false,"filename"=>'myfile'),$addparams);
	$str_srv_uploader = serv_url('mod_fileload/fileload',$addparams);
	$str_js="
	var settings = {
    url: \"$str_srv_uploader\",
    dragDrop:true,
    fileName: \"".$addparams["filename"]."\",
    allowedTypes:\"jpg,png,gif,doc,pdf,zip\",	
    returnType:\"json\",
	 onSuccess:function(files,data,xhr)
    {
       // alert((data));
    },
    showDelete:true,
    deleteCallback: function(data,pd)
	{
    for(var i=0;i<data.length;i++)
    {
        $.post(\"delete.php\",{op:\"delete\",name:data[i]},
        function(resp, textStatus, jqXHR)
        {
            //Show Message  
            $(\"#status\").append(\"<div>File Deleted</div>\");      
        });
     }      
    pd.statusbar.hide(); //You choice to hide/not.

}
}		
			
			\$(\"".$addparams['selector']."\").uploadFile({
url:\"$str_srv_uploader\",
multiple:true,
fileName:\"".$addparams["filename"]."\"
});";
	jqready_gather($str_js);

}
?>